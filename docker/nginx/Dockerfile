ARG APP_IMAGE

FROM ${APP_IMAGE} AS assets

FROM nginx:mainline-alpine

# auto_envsubst 
# https://github.com/nginxinc/docker-nginx/blob/793319d7251c03eccecbf27b60e0cfbbd2d1f400/mainline/alpine/20-envsubst-on-templates.sh
ENV NGINX_ENVSUBST_TEMPLATE_DIR="/etc/nginx/conf.d" \
    NGINX_ENVSUBST_TEMPLATE_SUFFIX=".tmpl"

# default
ENV NGINX_PORT="80" \
    NGINX_APP_HOST="app" \
    NGINX_APP_PORT="9000" \
    NGINX_EXPORTER_SUBNET="172.16.0.0/12" 


COPY ./docker/nginx /etc/nginx
COPY --from=assets /app/public /app/public
