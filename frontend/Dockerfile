FROM node:22-alpine

WORKDIR /app

# Copy package files first for better caching
COPY package.json package-lock.json ./

# Install Node dependencies
RUN npm install

# Copy the rest of the app
COPY . .

# Expose port
EXPOSE 8080

# Start the dev server
CMD ["npm", "run", "serve"]
