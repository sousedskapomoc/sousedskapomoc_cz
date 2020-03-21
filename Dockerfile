FROM nginx:latest
MAINTAINER Astronaut apelttom@gmail.com

RUN apt-get -y update && apt-get -y upgrade

# remove default config so we can replace it with ours
RUN rm /etc/nginx/conf.d/default.conf

# if the configuration still has .dev appendix, rename it
RUN if [ -f ./app/config/local.neon.dev ]; then mv ./app/config/local.neon.dev ./app/config/local.neon; fi

COPY ./sousedskapomoc.cz.conf /etc/nginx/conf.d/
