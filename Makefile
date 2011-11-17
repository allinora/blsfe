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

deploydev:
	ssh root@tipi.lilarox.com make -C /opt/git/blsfe pull

deploydreamhost:
	rsync -av --exclude=.git -e ssh . tipi@abudhabi.dreamhost.com:blsfe/


