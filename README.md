# LUMINIX
Luminix is a subscription service app for streaming TV shows, movies, and original content on demand.<br>
Its core functions include:<br>
• Watching content with personalized recommendations.<br>
• Creating individual profiles for users.

# Installation
git clone <br>
and setup data by creat Tabel and then downlond data at IMDB<br>
Data: https://developer.imdb.com/non-commercial-datasets/?utm_source=chatgpt.com<br>
control c in mac to close<br>
SETUP DATA<br>
**IMDb Table --> Luminix Table : Key **<br>
title.basics --> Content : tconst --> ContentID<br>
title.crew --> ContentCrew : tconst --> ContentID<br>
title.episode --> Episode : tconst --> EpisodeID , parentTconst → SeriesID<br>
name.basics -->Person : nconst --> PersonID<br>
title.principals --> ContentPerson : tconst → ContentID, nconst → PersonID<br>
title.ratings --> Rating : tconst → ContentID<br>
for big data use py code to lode data to mysql
# Usage
after setup data<br>
run in terminal<br>
cd [filepath ]<br>
php -S localhost:8080<br>
than open at http://localhost:8080<br>
# Tech Stack
** Front End ** <br>
HTML CSS JVS<br>
** Back End ** <br>
PHP MySQL <br>

# Author
https://github.com/Nattapat23<br>
https://github.com/Maybemabell<br>
https://github.com/Phutawa?fbclid=IwZXh0bgNhZW0CMTEAAR4loWhaV11xxbq_ZXEofOx_Rsanpr9rd-btBLp8rgjfQg1y5SvVCJPgOKxLNw_aem_4DOOfowOQkQFE5Jguz-f_g<br>


