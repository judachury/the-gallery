/*This script is necessary to keep the images in The Gallery
Please run it before you deploy your site to a real environment*/
CREATE TABLE image
(
	id INT(11) AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(20) NOT NULL,
	description VARCHAR(100),
	unique_file_name VARCHAR(13),
	image_width int,
	image_height int	
)
ENGINE=InnoDB;