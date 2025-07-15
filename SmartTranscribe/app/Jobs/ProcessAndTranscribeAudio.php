<?php

namespace App\Jobs;

use App\Libraries\AudioProcessorLibrary;
use App\Libraries\Azure\TranscriptionClient;
use App\Models\Transcription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProcessAndTranscribeAudio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1000;

    protected array $data;
    protected Transcription $transcription;

    private array $temp_files = [];

    private const TEMP_DISK = 'public';
    private const REMOTE_DISK = 's3';
    private const TEMP_DIR = 'temp/';
    private const REMOTE_CONVERTED_DIR = 'converted/';

    public function __construct(array $data, Transcription $transcription)
    {
        $this->data = $data;
        $this->transcription = $transcription;
    }

    public function handle(
        AudioProcessorLibrary $audioProcessor,
        TranscriptionClient $transcriptionClient
    ) {
        try {
            $originalFilePath = $this->downloadRemoteFile($this->data['media_url']);

            $cloudAudioUrl = $this->processAndUploadAsSteam($originalFilePath, $audioProcessor);

            $transcriptionData = $this->requestTranscription($cloudAudioUrl, $transcriptionClient);

            $this->updateTranscriptionRecord($transcriptionData, $cloudAudioUrl);

            Log::info('Transcription job completed successfully for ' . $this->data['media_url']);
        } catch (Throwable $e) {
            $this->handleFailure($e);
            throw $e;
        } finally {
            $this->cleanupTempFiles();
        }
    }

    private function downloadRemoteFile(string $mediaUrl): string
    {
        Log::info("Starting download from: " . $mediaUrl);
        $fileContentsStream = fopen($mediaUrl, 'r');
        if ($fileContentsStream === false) {
            throw new \Exception("Could not open remote URL: {$mediaUrl}");
        }

        $filename = Str::random(40) . '.' . pathinfo($mediaUrl, PATHINFO_EXTENSION);
        $relativePath = self::TEMP_DIR . $filename;

        Storage::disk(self::TEMP_DISK)->put($relativePath, $fileContentsStream);

        $this->temp_files[] = $relativePath;
        Log::info("File downloaded to temporary path: " . $relativePath);

        return $relativePath;
    }

    private function processAndUploadAsSteam(string $localRelativePath): string
    {
        $localAbsolutePath = Storage::disk(self::TEMP_DISK)->path($localRelativePath);

        $convertedAudioFilename = Str::random(40) . '.wav';
        $convertedAudioRelativePath = self::TEMP_DIR . $convertedAudioFilename;
        $convertedAudioAbsolutePath = Storage::disk(self::TEMP_DISK)->path($convertedAudioRelativePath);

        $this->temp_files[] = $convertedAudioRelativePath;

        $escaped_input = escapeshellarg($localAbsolutePath);

        $escaped_output = escapeshellarg($convertedAudioAbsolutePath);
        $command = "ffmpeg -i {$escaped_input} -y -vn -acodec pcm_s16le -ar 16000 -ac 1 -f wav {$escaped_output}";

        Log::info('Starting local FFmpeg conversion.', ['command' => $command]);

        $process = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);

        if (is_resource($process)) {
            $ffmpeg_stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $ffmpeg_error_output = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $return_value = proc_close($process);

            if ($return_value !== 0) {
                Log::error("FFmpeg local conversion failed", [
                    'exit_code' => $return_value,
                    'error_output' => $ffmpeg_error_output,
                    'stdout' => $ffmpeg_stdout,
                ]);
                throw new \Exception("FFmpeg local conversion failed with exit code {$return_value}: " . $ffmpeg_error_output);
            }

            Log::info("FFmpeg conversion to local file successful: " . $convertedAudioAbsolutePath);
        } else {
            throw new \Exception("Failed to open process for FFmpeg command.");
        }

        $remotePath = self::REMOTE_CONVERTED_DIR . $convertedAudioFilename;
        Log::info('Uploading converted file to remote storage.', ['local' => $convertedAudioRelativePath, 'remote' => $remotePath]);

        Storage::disk(self::REMOTE_DISK)->putFileAs(self::REMOTE_CONVERTED_DIR, $convertedAudioAbsolutePath, $convertedAudioFilename);

        $cloudAudioUrl = Storage::disk(self::REMOTE_DISK)->url($remotePath);
        Log::info("Converted audio uploaded to: " . $cloudAudioUrl);

        return $cloudAudioUrl;
    }

    private function requestTranscription(string $audioUrl, TranscriptionClient $transcriptionClient): array
    {
        Log::info("Requesting transcription for: " . $audioUrl);
        return $transcriptionClient->generateTranscription($audioUrl);
    }

    private function updateTranscriptionRecord(array $transcriptionData, string $cloudAudioUrl): void
    {
        $this->transcription->update([
            'external_id' => $transcriptionData['external_id'],
            'filename'    => $transcriptionData['filename'],
            'content'     => $transcriptionData['content'],
            'status'      => $transcriptionData['status'],
            'audio'       => $cloudAudioUrl
        ]);
    }

    private function handleFailure(Throwable $e): void
    {
        Log::error('Error processing transcription job: ' . $e->getMessage(), [
            'transcription_id' => $this->transcription->id,
            'file'             => $e->getFile(),
            'line'             => $e->getLine(),
            'trace'            => $e->getTraceAsString()
        ]);

        $this->transcription->update(['status' => Transcription::STATUS_FAILED]);
    }

    private function cleanupTempFiles(): void
    {
        if (empty($this->temp_files)) {
            return;
        }

        Log::info('Cleaning up temporary files...', ['files' => $this->temp_files]);
        Storage::disk(self::TEMP_DISK)->delete($this->temp_files);
        $this->temp_files = [];
    }
}
