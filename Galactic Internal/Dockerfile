# Stage 1: Build stage with a shell and PHP CLI
FROM php:8.2-cli AS builder

# Copy application files
COPY . /var/www/html/

# Set the working directory
WORKDIR /var/www/html/

# Run the database initialization script
RUN php galacticdb-init.php

# Stage 2: Final stage with distroless image
FROM cgr.dev/chainguard/php:latest

# Copy the application files from the build stage
COPY --from=builder /var/www/ /var/www/

# Expose port 80
EXPOSE 80

# Use CMD to run PHP's built-in web server
CMD ["-S", "0.0.0.0:80", "-t", "/var/www/html"]
