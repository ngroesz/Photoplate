function toggleThumbGenerate() {
	if (document.image_form.thumb_auto_generate.checked) {
		document.getElementById("thumb").style.display = "none";
		document.getElementById("thumb_width").style.display = "block";
		document.getElementById("thumb_height").style.display = "block";
	} else {
		document.getElementById("thumb").style.display = "block";
		document.getElementById("thumb_width").style.display = "none";
		document.getElementById("thumb_height").style.display = "none";
	}
}
