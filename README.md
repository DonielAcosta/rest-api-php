# Levantar Servidor

 php -S localhost:8000 

# añadirdata 
  curl -X 'POST' "http://localhost:8000/books" -d '{ "titulo": "drago ball","id_autor": 1, "id_genero": 2 }'
