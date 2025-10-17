# LUMINIX
Luminix is a subscription service app for streaming TV shows, movies, and original content on demand.<br>
Its core functions include:<br>
• Watching content with personalized recommendations.<br>
• Creating individual profiles for users.

# Installation
git clone <br>
and setup data by creat Tabel and then downlond data at IMDB<br>
Data: https://developer.imdb.com/non-commercial-datasets/?utm_source=chatgpt.com<br>
SETUP DATA<br>
**IMDb Table --> Luminix Table : Key **<br>
title.basics --> Content : tconst --> ContentID<br>
title.crew --> ContentCrew : tconst --> ContentID<br>
title.episode --> Episode : tconst --> EpisodeID , parentTconst → SeriesID<br>
name.basics -->Person : nconst --> PersonID<br>
title.principals --> ContentPerson : tconst → ContentID, nconst → PersonID<br>
title.ratings --> Rating : tconst → ContentID<br>
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



