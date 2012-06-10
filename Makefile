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

deploydev:
	ssh root@tipi.lilarox.com make -C /opt/git/blsfe pull

deploydreamhost:
	rsync -av --exclude=.git -e ssh . tipi@abudhabi.dreamhost.com:blsfe/

css:
	lessc assets/js/jquery/cowrousel/cowrousel.less.css assets/js/jquery/cowrousel/cowrousel.css
