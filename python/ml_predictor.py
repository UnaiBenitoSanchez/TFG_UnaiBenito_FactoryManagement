import mysql.connector
import json
import numpy as np
from sklearn.linear_model import LinearRegression

def get_boss_factory(boss_id):
    try:
        connection = mysql.connector.connect(
            host="localhost",
            user="unai",      
            password="xd",     
            database="GestionDeFabricas"
        )
        cursor = connection.cursor()

        query = """
            SELECT factory_id_factory
            FROM factory_boss
            WHERE boss_id_boss_factory = %s
        """
        cursor.execute(query, (boss_id,))
        factory = cursor.fetchone()

        if factory:
            return factory[0]  
        else:
            return None

    except mysql.connector.Error as err:
        return {"error": str(err)}

    finally:
        if 'connection' in locals() and connection.is_connected():
            cursor.close()
            connection.close()

def get_real_data(boss_id):
    try:
        factory_id = get_boss_factory(boss_id)
        if not factory_id:
            return {"error": "Could not find factory for this boss."}

        connection = mysql.connector.connect(
            host="localhost",
            user="unai",      
            password="xd",   
            database="GestionDeFabricas"
        )
        cursor = connection.cursor()

        query = """
            SELECT p.id_product, p.name 
            FROM product p
            JOIN inventory i ON p.id_product = i.product_id_product
            WHERE i.factory_id_factory = %s
            ORDER BY p.id_product
        """
        cursor.execute(query, (factory_id,))
        rows = cursor.fetchall()

        if not rows:
            return {"error": "No products found for this factory."}

        products = {}
        for product_id, name in rows:
            products[product_id] = {"name": name, "quantity": []}

        history_query = """
            SELECT product_id_product, change_quantity
            FROM inventory_history
            WHERE product_id_product IN (%s)
            ORDER BY product_id_product, change_timestamp
        """

        product_keys = ','.join([str(pid) for pid in products.keys()])
        
        cursor.execute(history_query % product_keys)
        history_rows = cursor.fetchall()

        if not history_rows:
            return {"error": "No inventory change records found for products."}

        for product_id, quantity in history_rows:
            if product_id in products:
                products[product_id]["quantity"].append(quantity)

        if not any(len(info["quantity"]) > 0 for info in products.values()):
            return {"error": "Not enough inventory data for products."}

        return products

    except mysql.connector.Error as err:
        return {"error": str(err)}

    finally:
        if 'connection' in locals() and connection.is_connected():
            cursor.close()
            connection.close()

def predict_demands(boss_id):
    data = get_real_data(boss_id)

    if "error" in data:
        return data

    predictions = {}

    for product_id, product_info in data.items():
        quantities = product_info["quantity"]
        if len(quantities) < 2:
            predictions[product_id] = f"{product_info['name']}: Insufficient data"
            continue

        X = np.array(range(len(quantities))).reshape(-1, 1)  
        y = np.array(quantities)  

        model = LinearRegression()
        model.fit(X, y)
        next_day = np.array([[len(quantities)]])  
        pred = model.predict(next_day)[0]

        predictions[product_id] = {
            "name": product_info["name"],
            "prediction": round(pred, 2)
        }

    return {"predictions": predictions}

if __name__ == "__main__":
    boss_id = 1 
    result = predict_demands(boss_id)
    print(json.dumps(result))