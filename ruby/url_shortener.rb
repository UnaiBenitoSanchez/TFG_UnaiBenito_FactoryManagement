#!/usr/bin/env ruby
# encoding: UTF-8

# Instalación de gemas requeridas:
# gem install sinatra securerandom rqrcode sqlite3

require 'sinatra'
require 'securerandom'
require 'rqrcode'
require 'sqlite3'
require 'base64'

class URLShortener
  def initialize
    # Inicializar la base de datos SQLite
    @db = SQLite3::Database.new('urls.db')
    create_table
  end

  def create_table
    # Crear tabla urls si no existe
    @db.execute <<-SQL
      CREATE TABLE IF NOT EXISTS urls (
        id INTEGER PRIMARY KEY,
        original_url TEXT NOT NULL,
        short_code TEXT NOT NULL UNIQUE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
      );
    SQL
  end

  def shorten(url)
    # Generar un código corto aleatorio de 6 caracteres
    short_code = SecureRandom.alphanumeric(6)
    
    # Verificar que el código no exista ya en la base de datos
    while code_exists?(short_code)
      short_code = SecureRandom.alphanumeric(6)
    end
    
    # Guardar la URL original y su código corto en la base de datos
    @db.execute("INSERT INTO urls (original_url, short_code) VALUES (?, ?)", [url, short_code])
    
    return short_code
  end

  def get_original_url(short_code)
    # Obtener la URL original a partir del código corto
    result = @db.get_first_value("SELECT original_url FROM urls WHERE short_code = ?", short_code)
    return result
  end

  def code_exists?(short_code)
    # Verificar si un código corto ya existe en la base de datos
    result = @db.get_first_value("SELECT COUNT(*) FROM urls WHERE short_code = ?", short_code)
    return result.to_i > 0
  end

  def generate_qr(url)
    # Generar un código QR para la URL
    qrcode = RQRCode::QRCode.new(url)
    
    # Convertir el código QR a PNG
    png = qrcode.as_png(
      resize_gte_to: false,
      resize_exactly_to: false,
      fill: 'white',
      color: 'black',
      size: 240,
      border_modules: 4,
      module_px_size: 6
    )
    
    # Devolver los datos del PNG en formato Base64
    return Base64.strict_encode64(png.to_s)
  end
end

# Configuración de la aplicación Sinatra
set :port, 4567
set :bind, '0.0.0.0'

# Inicializar el acortador de URL
shortener = URLShortener.new

# Ruta principal - formulario para acortar URLs
get '/' do
  <<-HTML
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>Acortador de URLs y Generador QR</title>
      <style>
        body {
          font-family: Arial, sans-serif;
          max-width: 800px;
          margin: 0 auto;
          padding: 20px;
        }
        .container {
          background-color: #f5f5f5;
          border-radius: 10px;
          padding: 20px;
          box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
          color: #333;
        }
        input[type="url"] {
          width: 100%;
          padding: 10px;
          margin: 10px 0;
          border: 1px solid #ddd;
          border-radius: 4px;
        }
        button {
          background-color: #4CAF50;
          color: white;
          padding: 10px 15px;
          border: none;
          border-radius: 4px;
          cursor: pointer;
        }
        button:hover {
          background-color: #45a049;
        }
        .result {
          margin-top: 20px;
          padding: 15px;
          background-color: #fff;
          border-radius: 4px;
          border: 1px solid #ddd;
        }
        .qr-code {
          text-align: center;
          margin: 20px 0;
        }
      </style>
    </head>
    <body>
      <div class="container">
        <h1>Acortador de URLs y Generador QR</h1>
        <form action="/shorten" method="post">
          <label for="url">Ingresa la URL para acortar:</label>
          <input type="url" id="url" name="url" placeholder="https://ejemplo.com/ruta/muy/larga" required>
          <button type="submit">Acortar y Generar QR</button>
        </form>
      </div>
    </body>
    </html>
  HTML
end

# Ruta para procesar la URL enviada
post '/shorten' do
  original_url = params[:url]
  
  # Validar que la URL sea válida
  unless original_url.start_with?('http://', 'https://')
    original_url = "https://#{original_url}"
  end
  
  # Acortar la URL
  short_code = shortener.shorten(original_url)
  
  # Crear la URL corta
  short_url = "#{request.base_url}/#{short_code}"
  
  # Generar el código QR para la URL corta
  qr_code_base64 = shortener.generate_qr(short_url)
  
  # Mostrar los resultados
  <<-HTML
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>URL Acortada y Código QR</title>
      <style>
        body {
          font-family: Arial, sans-serif;
          max-width: 800px;
          margin: 0 auto;
          padding: 20px;
        }
        .container {
          background-color: #f5f5f5;
          border-radius: 10px;
          padding: 20px;
          box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
          color: #333;
        }
        .result {
          margin-top: 20px;
          padding: 15px;
          background-color: #fff;
          border-radius: 4px;
          border: 1px solid #ddd;
        }
        .qr-code {
          text-align: center;
          margin: 20px 0;
        }
        .short-url {
          font-weight: bold;
          color: #4CAF50;
          word-break: break-all;
        }
        .back {
          display: inline-block;
          margin-top: 20px;
          color: #2196F3;
          text-decoration: none;
        }
      </style>
    </head>
    <body>
      <div class="container">
        <h1>¡URL Acortada con Éxito!</h1>
        
        <div class="result">
          <h2>URL Original:</h2>
          <p>#{original_url}</p>
          
          <h2>URL Acortada:</h2>
          <p class="short-url">#{short_url}</p>
        </div>
        
        <div class="qr-code">
          <h2>Código QR:</h2>
          <img src="data:image/png;base64,#{qr_code_base64}" alt="QR Code">
          <p>Escanea este código QR para acceder a la URL acortada.</p>
        </div>
        
        <a href="/" class="back">Acortar otra URL</a>
      </div>
    </body>
    </html>
  HTML
end

# Ruta para redirigir a la URL original
get '/:short_code' do
  short_code = params[:short_code]
  original_url = shortener.get_original_url(short_code)
  
  if original_url
    redirect original_url
  else
    status 404
    "URL no encontrada"
  end
end

# Iniciar el servidor si este archivo es ejecutado directamente
if __FILE__ == $0
  puts "Servidor iniciado en http://localhost:4567"
  Sinatra::Application.run!
end