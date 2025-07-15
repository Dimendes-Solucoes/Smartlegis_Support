export function getImageUrl(path: string | null): string {
    if (!path) {
        return ''
    }
    const baseUrl = import.meta.env.VITE_S3_BASE_URL || 'https://meu-bucket.s3.amazonaws.com'
    return `${baseUrl.replace(/\/$/, '')}/${path.replace(/^\//, '')}`
}