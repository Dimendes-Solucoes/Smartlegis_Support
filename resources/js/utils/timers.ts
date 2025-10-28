export const applyTimerMask = (value: string): string => {
    const cleanedValue = value.replace(/\D/g, '').substring(0, 6);

    const formattedValue = cleanedValue.replace(/^(\d{2})(\d{2})?(\d{2})?$/, (match, p1, p2, p3) => {
        let result = p1;
        if (p2) {
            result += `:${p2}`;
        }
        if (p3) {
            result += `:${p3}`;
        }
        return result;
    });

    return formattedValue;
};

export const formatTimer = (timeString: string): string => {
    if (!timeString || typeof timeString !== 'string') {
        return '';
    }

    const parts = timeString.split(':').map(part => parseInt(part, 10));

    const [hours, minutes, seconds] = parts;

    if (parts.length !== 3 || parts.some(isNaN)) {
        return '';
    }

    const totalSeconds = (hours * 3600) + (minutes * 60) + seconds;
    const displayMinutes = Math.floor(totalSeconds / 60);
    const displaySeconds = totalSeconds % 60;

    const formattedParts = [];

    if (displayMinutes > 0) {
        formattedParts.push(`${displayMinutes} min`);
    }

    if (displaySeconds > 0 || totalSeconds === 0) {
        formattedParts.push(`${displaySeconds} seg`);
    }

    return formattedParts.join(' e ');
};