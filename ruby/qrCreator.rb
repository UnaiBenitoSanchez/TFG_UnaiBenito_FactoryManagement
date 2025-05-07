#!/usr/bin/env ruby
# encoding: UTF-8

# Required gems:
# gem install sinatra rqrcode

require 'sinatra'
require 'rqrcode'
require 'base64'

class QRGenerator
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

qr_generator = QRGenerator.new

get '/' do
  <<-HTML
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>QR generator for URLs</title>
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
      </style>
    </head>
    <body>
      <div class="container">
        <h1>QR generator for URLs</h1>
        <form action="/generate-qr" method="post">
          <label for="url">Insert the URL:</label>
          <input type="url" id="url" name="url" placeholder="https://example.com" required>
          <button type="submit">Generate QR</button>
        </form>
      </div>
    </body>
    </html>
  HTML
end

post '/generate-qr' do
  original_url = params[:url]
  
  unless original_url.start_with?('http://', 'https://')
    original_url = "https://#{original_url}"
  end
  
  qr_code_base64 = qr_generator.generate_qr(original_url)
  
  <<-HTML
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <title>Generated QR code</title>
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
        .url {
          font-weight: bold;
          word-break: break-all;
          color: #2196F3;
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
        <h1>QR code generated succesfully</h1>
        
        <div class="result">
          <h2>URL:</h2>
          <p class="url">#{original_url}</p>
        </div>
        
        <div class="qr-code">
          <h2>QR Code:</h2>
          <img src="data:image/png;base64,#{qr_code_base64}" alt="QR Code">
        </div>
        
        <a href="/" class="back">Generate another QR code</a>
      </div>
    </body>
    </html>
  HTML
end

if __FILE__ == $0
  puts "Server started in http://localhost:4567"
  Sinatra::Application.run!
end