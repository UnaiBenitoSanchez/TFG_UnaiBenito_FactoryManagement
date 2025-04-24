function predecirDemanda(historial) {
    fetch('../php/predict_demand.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'historial[]=' + historial.join('&historial[]='),
    })
      .then(res => res.json())
      .then(data => {
        console.log('Demanda esperada:', data.prediccion);
        // Aquí puedes mostrarlo en pantalla por ejemplo:
        document.getElementById('resultado').innerText = 'Demanda esperada: ' + data.prediccion;
      });
  }
  