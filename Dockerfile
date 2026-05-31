cd cc
echo "FROM alpine:latest" > Dockerfile
echo "WORKDIR /app" >> Dockerfile
echo "COPY . ." >> Dockerfile
echo "CMD [\"sh\"]" >> Dockerfile