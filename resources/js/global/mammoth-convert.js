import mammoth from 'mammoth';

const path = process.argv[2];

mammoth.convertToHtml({path: path})
    .then(result => console.log(result.value))
    .catch(err => process.exit(1));