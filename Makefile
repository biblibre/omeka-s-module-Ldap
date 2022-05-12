MODULE := $(notdir $(CURDIR))
VERSION := $(shell php -r 'echo parse_ini_file("config/module.ini")["version"];')
ZIP := ${MODULE}-${VERSION}.zip

dist: ${ZIP}

${ZIP}:
	rm -f $@
	git archive -o $@ --prefix=${MODULE}/ HEAD
	composer -q install --no-dev
	mkdir -p ${MODULE} && ln -sr vendor ${MODULE}/vendor && zip -q -r $@ ${MODULE} && rm ${MODULE}/vendor && rmdir ${MODULE}

.PHONY: ${ZIP}
