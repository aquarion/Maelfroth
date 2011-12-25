<?PHP

class lampstand extends AqWikiMacro {

	function init(){
		return True;
	}

	function display(){
		return file_get_contents('https://raw.github.com/aquarion/lampstand/master/README.textile');
	}
}
