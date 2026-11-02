# PHP-FPM 8.3 for studios.boomroom.by on boom-ubuntu (boom-studios stack), same base as boom-site.
# The apache container passes *.php here on port 9000; FPM runs as container root (= boom-studios on the host).
FROM php:8.3.36-fpm-bookworm@sha256:6b4641a875dd3e0f7551b2751e1f5909084856d18166af7fbb522ac230feb702

RUN set -eux; \
	docker-php-ext-install -j"$(nproc)" mysqli opcache; \
	apt-get update; \
	DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends msmtp; \
	rm -rf /var/lib/apt/lists/*; \
	php -m

COPY php.ini /usr/local/etc/php/conf.d/zz-studios.ini
# zzz- so it loads after the image's zz-docker.conf (which sets listen = 9000)
COPY pool.conf /usr/local/etc/php-fpm.d/zzz-studios.conf
COPY msmtprc /etc/msmtprc
