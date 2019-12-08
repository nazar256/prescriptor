FROM nginx
ARG PUID
RUN if [ ! -z "$PUID" ]; then echo "changing nginx user id to $PUID" && usermod -u "$PUID" nginx; fi