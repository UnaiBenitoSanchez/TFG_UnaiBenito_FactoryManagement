import pymysql

# Conexión a MySQL
conn = pymysql.connect(
    host='localhost',
    user='unai',
    password='xd',
    db='GestionDeFabricas'
)
cursor = conn.cursor()

imagenes = {
    1: "../img/mattel1.jpg",
    2: "../img/mattel2.jpg",
    3: "../img/mattel3.jpg",
    4: "../img/mattel4.jpg",
    5: "../img/mattel5.jpg",
    6: "../img/mattel6.jpg",
    7: "../img/lego1.jpg",
    8: "../img/lego2.jpg",
    9: "../img/lego3.jpg",
    10: "../img/lego4.jpg",
    11: "../img/lego5.jpg",
    12: "../img/lego6.jpg",
    13: "../img/nerf1.jpg",
    14: "../img/nerf2.jpg",
    15: "../img/nerf3.jpg",
    16: "../img/nerf4.jpg",
    17: "../img/nerf5.jpg",
    18: "../img/nerf6.jpg",
    19: "../img/playmobil1.jpg",
    20: "../img/playmobil2.jpg",
    21: "../img/playmobil3.jpg",
    22: "../img/playmobil4.jpg",
    23: "../img/playmobil5.jpg",
    24: "../img/playmobil6.jpg",
    25: "../img/playmobil7.jpg",
    26: "../img/playmobil8.jpg"
}

# Insertar imágenes como LONGBLOB
for product_id, img_path in imagenes.items():
    with open(img_path, 'rb') as file:
        img_blob = file.read()
        sql = "UPDATE product SET image_data = %s WHERE id_product = %s"
        cursor.execute(sql, (img_blob, product_id))

# Guardar cambios
conn.commit()
cursor.close()
conn.close()
print("Todas las imágenes han sido insertadas como LONGBLOB.")
