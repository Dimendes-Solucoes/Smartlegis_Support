export const applyTimerMask = (value: string): string => {
    let cleanedValue = value.replace(/\D/g, '');

    if (cleanedValue.length > 6) {
        cleanedValue = cleanedValue.substring(0, 6);
    }

    let formattedValue = '';
    if (cleanedValue.length > 0) {
        formattedValue += cleanedValue.substring(0, 2);
    }
    if (cleanedValue.length > 2) {
        formattedValue += ':' + cleanedValue.substring(2, 4);
    }
    if (cleanedValue.length > 4) {
        formattedValue += ':' + cleanedValue.substring(4, 6);
    }

    return formattedValue;
};

export const formatTimer = (timeString: string): string => {
    if (!timeString || typeof timeString !== 'string') {
        return '';
    }

    const parts = timeString.split(':');
    if (parts.length !== 3) {
        return '';
    }

    const hours = parseInt(parts[0], 10);
    const minutes = parseInt(parts[1], 10);
    const seconds = parseInt(parts[2], 10);

    const totalSeconds = (hours * 3600) + (minutes * 60) + seconds;

    const displayMinutes = Math.floor(totalSeconds / 60);
    const displaySeconds = totalSeconds % 60;

    let formattedParts = [];

    if (displayMinutes > 0) {
        formattedParts.push(`${displayMinutes} min`);
    }
    if (displaySeconds > 0 || (displayMinutes === 0 && displaySeconds === 0)) {
        formattedParts.push(`${displaySeconds} seg`);
    }

    return formattedParts.join(' e ');
};