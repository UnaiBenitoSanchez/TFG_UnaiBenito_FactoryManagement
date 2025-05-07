#!/usr/bin/env ruby
# encoding: UTF-8

# Instalation of required gems:
# gem install sinatra securerandom rqrcode sqlite3

require 'sinatra'
require 'securerandom'
require 'rqrcode'
require 'sqlite3'
require 'base64'

class URLShortener
  def initialize
    @db = SQLite3::Database.new('urls.db')
    create_table
  end

  def create_table
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
    short_code = SecureRandom.alphanumeric(6)
    
    while code_exists?(short_code)
      short_code = SecureRandom.alphanumeric(6)
    end
    
    @db.execute("INSERT INTO urls (original_url, short_code) VALUES (?, ?)", [url, short_code])
    
    return short_code
  end

  def get_original_url(short_code)
    result = @db.get_first_value("SELECT original_url FROM urls WHERE short_code = ?", short_code)
    return result
  end

  def code_exists?(short_code)
    result = @db.get_first_value("SELECT COUNT(*) FROM urls WHERE short_code = ?", short_code)
    return result.to_i > 0
  end

  def generate_qr(url)
    qrcode = RQRCode::QRCode.new(url)
    
    png = qrcode.as_png(
      resize_gte_to: false,
      resize_exactly_to: false,
      fill: 'white',
      color: 'black',
      size: 240,
      border_modules: 4,
      module_px_size: 6
    )
    
    return Base64.strict_encode64(png.to_s)
  end
end

set :port, 4567
set :bind, '0.0.0.0'

shortener = URLShortener.new

get '/' do
  <<-HTML
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>URL shortener and QR generator</title>
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
        <h1>URL shortener and QR generator</h1>
        <form action="/shorten" method="post">
          <label for="url">Insert the URL:</label>
          <input type="url" id="url" name="url" placeholder="https://example.com/longRoute" required>
          <button type="submit">Short and generate QR</button>
        </form>
      </div>
    </body>
    </html>
  HTML
end

post '/shorten' do
  original_url = params[:url]
  
  unless original_url.start_with?('http://', 'https://')
    original_url = "https://#{original_url}"
  end
  
  short_code = shortener.shorten(original_url)
  
  short_url = "#{request.base_url}/#{short_code}"
  
  qr_code_base64 = shortener.generate_qr(short_url)
  
  <<-HTML
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>Shortened URL and QR</title>
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
        <h1>URL shortened succesfully</h1>
        
        <div class="result">
          <h2>Original URL:</h2>
          <p>#{original_url}</p>
          
          <h2>URL Acortada:</h2>
          <p class="short-url">#{short_url}</p>
        </div>
        
        <div class="qr-code">
          <h2>Código QR:</h2>
          <img src="data:image/png;base64,#{qr_code_base64}" alt="QR Code">
        </div>
        
        <a href="/" class="back">Shorten another URLL</a>
      </div>
    </body>
    </html>
  HTML
end

get '/:short_code' do
  short_code = params[:short_code]
  original_url = shortener.get_original_url(short_code)
  
  if original_url
    redirect original_url
  else
    status 404
    "URL not found"
  end
end

if __FILE__ == $0
  puts "Server started in http://localhost:4567"
  Sinatra::Application.run!
end