default:
	echo Read the Makefile
install:
	mkdir -p tmp/cache tmp/logs tmp/sessions tmp/smarty_compile tmp/smarty_cache public/uploads/cms/images/spool
	chmod -R 777  tmp/cache tmp/logs tmp/sessions tmp/smarty_compile tmp/smarty_cache public/uploads

clean:
	rm -rf tmp
	find . -type f -name ".#*" -exec rm {} \;
	find . -type f -name "*~" -exec rm {} \;
	find . -type f -name "Thumbs.db" -exec rm {} \;
	find . -type f -name ".DS_Store" -exec rm {} \;


