FROM nginx:latest
MAINTAINER Astronaut apelttom@gmail.com

RUN apt-get -y update && apt-get -y upgrade

# remove default config so we can replace it with ours
RUN rm /etc/nginx/conf.d/default.conf
