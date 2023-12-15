FROM nginx

# config nginx
COPY .docker/nginx/default.conf /etc/nginx/conf.d

WORKDIR /var/www

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
