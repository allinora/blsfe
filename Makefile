default:
	echo Read the Makefile
install:
	echo Nothing to do
clean:
	echo Nothing to do

pull:
	git pull origin master

push:
	git push origin master
	git push assembla master
	git push github master

deploy: deployshowcase
	@echo all done

deployshowcase:
	rsync -av --exclude=.git  -e ssh . root@showcase.allinora.com:/opt/allinora/blsfe/

deploydev:
	ssh root@tipi.lilarox.com make -C /opt/git/blsfe pull

deploydreamhost:
	rsync -av --exclude=.git -e ssh . tipi@abudhabi.dreamhost.com:blsfe/

css:
	lessc assets/js/jquery/cowrousel/cowrousel.less.css assets/js/jquery/cowrousel/cowrousel.css

deployws:
	rsync -av -exclude=.git  -e ssh . root@wsdev01.worldsoft.ch:/opt/allinora/blsfe

deploywsgenesis:
	rsync -av -e ssh . root@admin.genesis.worldsoft.ch:/opt/allinora/blsfe

dist:
	rsync -av -e ssh . aghaffar@192.168.1.10:www/blsfe/
