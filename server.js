const express = require('express');
const path = require('path');
const phpExpress = require('php-express')({
    bin: 'C:/xampp/php/php.exe', // Cambia esta ruta a donde está PHP en tu máquina
    extensions: ['.php']
  });
  

const app = express();

// Configura Express para usar PHP con php-express
app.use('/public', express.static(path.join(__dirname, 'public')));
app.all('*.php', phpExpress.router);

// Sirve el archivo index.php
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'index.php'));
});

const port = 3000;
app.listen(port, () => {
  console.log(`Servidor PHP corriendo en http://localhost:${port}`);
});
