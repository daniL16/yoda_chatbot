FROM node:10

WORKDIR /usr/src/app/front
COPY package*.json ./
RUN npm install
EXPOSE 80

CMD ["npm", "run", "serve"]