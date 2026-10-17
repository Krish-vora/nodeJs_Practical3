
const express = require('express');

const app = express();

app.get('/students', async (req, res) => {
    try {
        const response = await fetch(
            'http://localhost/q6_php_api/students.php'
        );

        if (!response.ok) {
            return res.status(502).send('PHP API failed');
        }

        const students = await response.json();

        res.json(students);
    } catch (error) {
        res.status(500).send('Error calling PHP API');
    }
});

app.listen(3001, () => {
    console.log('Express server running on port 3001');
});
