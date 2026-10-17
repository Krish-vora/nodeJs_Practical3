
const express = require('express');

const app = express();

app.get('/students', (req, res) => {
    res.json([
        { id: 1, name: 'Rahul', course: 'MSc ICT' },
        { id: 2, name: 'Amit', course: 'BCA' }
    ]);
});

app.listen(3000, () => {
    console.log('Server running on port 3000');
});
