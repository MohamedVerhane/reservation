# Generate self-signed SSL cert for development/testing
# For production, use Let's Encrypt or your own CA

mkdir -p docker/nginx/ssl

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout docker/nginx/ssl/key.pem \
    -out docker/nginx/ssl/cert.pem \
    -subj "/C=US/ST=Local/L=Local/O=AuthApp/CN=localhost"

echo "Self-signed SSL cert generated in docker/nginx/ssl/"
