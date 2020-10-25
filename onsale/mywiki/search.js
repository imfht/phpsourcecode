var fs = require('fs'), removeMd = require('remove-markdown'), lunr = require('./static/js/lunr.min.js');

var md_index = lunr(function () {
	this.field('title');
	this.field('tags');
	this.field('body');
	this.ref('url');
});

function walk_dir(wdir, path, callback) {
	var dirList = fs.readdirSync(wdir + path);
	dirList.forEach(function(item) {
		if(fs.statSync(wdir + path + '/' + item).isDirectory())
			walk_dir(wdir, path + '/' + item, callback);
		else
			callback(wdir, path + '/' + item);
	});
}

function process_markdown(wdir, path) {
	var pos = path.lastIndexOf(".");
	if (pos < 0) return;
	if (path.substr(pos) != ".md" && path.substr(pos) != ".markdown") return;
	var fpos = path.lastIndexOf("/");
	var title = (fpos >= 0 ? path.substring(fpos + 1, pos) : path.substr(0, pos));

	// ignore navigation menu document
	if (path == "/navigation.md") return;

	var has_front = false;
	var md_doc = {
		"url": path,
		"title": title,
		"tags": ""
	};

	var data = fs.readFileSync(wdir + path, "utf-8");
	// ignore auto generated index.md
	if (title == "index" && data.indexOf("Auto-index of") >= 0) return;

	// process front matter
	if (data.substr(0, 3) == "```") {
		pos = data.substr(3).indexOf("```");
		if (pos >= 0) {
			var front = JSON.parse("{" + data.substr(3, pos) + "}");
			has_front = true;
			if ("title" in front) md_doc.title = front.title;
			if ("tags" in front) md_doc.tags = front.tags.join(' ');
			md_doc.body = removeMd(data.substr(pos + 6));
		}
	}
	if (!has_front) md_doc.body = removeMd(data);
	md_index.add(md_doc);
}

if (process.argv.length < 4) {
	console.log("Usage:\n");
	console.log(process.argv[0] + " " + process.argv[1] + " Wiki-document-directory Search-index-file");
	console.log(process.argv[0] + " " + process.argv[1] + " Search-index-file Keyword");
	process.exit(1);
} else if (!fs.existsSync(process.argv[2])) {
	console.log("Invalid Wiki-document-directory or Search-index-file: " + process.argv[2]);
	process.exit(1);
}

if (fs.statSync(process.argv[2]).isDirectory()) {
	walk_dir(process.argv[2], "", process_markdown);
	fs.writeFileSync(process.argv[3], JSON.stringify(md_index.toJSON()));
} else {
	md_index = lunr.Index.load(JSON.parse(fs.readFileSync(process.argv[2])));
	console.log(JSON.stringify(md_index.search(process.argv[3])));
}
