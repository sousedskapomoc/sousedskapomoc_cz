FROM sousedskapomoc/base-stack

ADD ./ /usr/share/nginx/html
WORKDIR /usr/share/nginx/html
RUN composer install
COPY ./config/local.neon.dev /usr/share/nginx/html/config/local.neon

