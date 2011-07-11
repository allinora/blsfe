default:
	echo Read the Makefile
install:
	mkdir -p files/smarty/cache  files/smarty/configs  files/smarty/templates  files/smarty/templates_c files/cache
	chmod 777 files/smarty/cache  files/smarty/configs  files/smarty/templates  files/smarty/templates_c files/cache

clean:
	rm -rf files/smarty/*/*  files/cache/tipiness.com/*
	find . -type f -name ".#*" -exec rm {} \;
	find . -type f -name "*~" -exec rm {} \;
	find . -type f -name "Thumbs.db" -exec rm {} \;
	find . -type f -name ".DS_Store" -exec rm {} \;


