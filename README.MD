ShortURL - PHP URL Shortener
Description
ShortURL is a standalone PHP class for URL shortening with reversible encoding and decoding. It supports optional storage in a JSON file or a MySQL database and includes a full API for CRUD operations.

Features
✅ Encode and decode short URLs deterministically
✅ Choose between JSON storage, MySQL, or no storage
✅ Full API with CRUD functionality
✅ Secure and scalable design
✅ Well-documented and easy to integrate

Installation
Clone the repository:

sh
Копировать
Редактировать
git clone https://github.com/yourusername/shorturl.git
cd shorturl
Configure storage (JSON or MySQL).

Use the class directly or set up API endpoints.

API Endpoints
Create Short URL: POST /shorten
Retrieve Original URL: GET /decode?code={short_code}
Update URL: POST /update
Delete Short URL: POST /delete
Contributing
Feel free to fork, contribute, or submit issues!