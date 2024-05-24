# Use the official Python image from the Docker Hub
FROM python:3.10-slim

# Set the working directory
WORKDIR /app

# Copy the requirements.txt file
COPY requirements.txt requirements.txt

# Install the required packages
RUN pip install -r requirements.txt

# Copy the rest of the application code
COPY . .

# Copy the configuration file into the container
COPY training.conf /etc/training.conf

# Set the environment variables
ENV AWS_REGION=us-east-1

# Expose port 80
EXPOSE 80

# Define the command to run the application
CMD ["python", "app.py"]
