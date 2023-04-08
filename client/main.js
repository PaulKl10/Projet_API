fetch("http://localhost:8000/public/index.php")
    .then((response)=>response.json())
    .then((res)=>res.forEach(r => {
        document.body.innerHTML += r['name']+" / "+r['priceHT']+" / "+r['description']+"<br><br>"
    }));