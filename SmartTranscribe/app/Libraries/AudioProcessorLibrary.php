<?php

namespace App\Libraries;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AudioProcessorLibrary
{
    public function convertFileToAudio(string $filepath): UploadedFile
    {
        $properties = $this->getAudioStreamProperties($filepath);

        if ($properties && $this->isStandardWav($properties)) {
            return new UploadedFile($filepath, basename($filepath), 'audio/wav', null, true);
        }

        return $this->performWavConversion($filepath);
    }

    private function isStandardWav(array $properties): bool
    {
        return ($properties['codec_name'] ?? '') === 'pcm_s16le' &&
            ($properties['sample_rate'] ?? 0) == 16000 &&
            ($properties['channels'] ?? 0) == 1;
    }

    private function getAudioStreamProperties(string $filepath): ?array
    {
        if (!file_exists($filepath)) {
            return null;
        }

        $escaped_filepath = escapeshellarg($filepath);
        $command = "ffprobe -v quiet -print_format json -show_streams {$escaped_filepath}";
        $output = shell_exec($command);
        $data = json_decode($output, true);

        if (isset($data['streams'])) {
            foreach ($data['streams'] as $stream) {
                if ($stream['codec_type'] === 'audio') {
                    return $stream;
                }
            }
        }

        return null;
    }

    private function performWavConversion(string $filepath): UploadedFile
    {
        $temp_path = storage_path('app/public/temp');
        if (!file_exists($temp_path)) {
            mkdir($temp_path, 0755, true);
        }

        $filename = Str::random(40);
        $output_path = "{$temp_path}/{$filename}.wav";

        $escaped_input = escapeshellarg($filepath);
        $escaped_output = escapeshellarg($output_path);

        exec("ffmpeg -i {$escaped_input} -y -vn -acodec pcm_s16le -ar 16000 -ac 1 {$escaped_output}");

        return new UploadedFile($output_path, basename($output_path), 'audio/wav', null, true);
    }
}
