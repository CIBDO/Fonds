export class NumberFormatter {
    static format(number) {
        if (!number && number !== 0) return '0';
        const num = typeof number === 'string' ?
            parseFloat(number.replace(/[^\d.-]/g, '')) :
            number;
        return !isNaN(num) ?
            num.toLocaleString('fr-FR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2,
                useGrouping: true
            }) : '0';
    }

    static unformat(formattedNumber) {
        if (!formattedNumber) return 0;
        return parseFloat(formattedNumber.toString()
            .replace(/\s/g, '')
            .replace(/\u202F/g, '')
            .replace(/[^\d.-]/g, '')
            .replace(',', '.')) || 0;
    }
}
