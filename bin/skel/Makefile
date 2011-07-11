default:
	echo Read the Makefile
install:
	mkdir -p tmp/cache tmp/logs tmp/sessions
	chmod 777  tmp/cache tmp/logs tmp/sessions

clean:
	rm -rf tmp
	find . -type f -name ".#*" -exec rm {} \;
	find . -type f -name "*~" -exec rm {} \;
	find . -type f -name "Thumbs.db" -exec rm {} \;
	find . -type f -name ".DS_Store" -exec rm {} \;


