<?php

/**
 * List all users with a link to edit
 */

require "../gb-config/config.php";
require "../gb-config/common.php";

try {
  $connection = new PDO($dsn, $username, $password, $options);

  $sql = "SELECT * FROM guestbook ";

  $statement = $connection->prepare($sql);
  $statement->execute();

  $result = $statement->fetchAll();
} catch(PDOException $error) {
  echo $sql . "<br>" . $error->getMessage();
}
?>
<?php require "templates/header.php"; ?>
        
<h2>View Guestbook Entries </h2>
<div class="table-container">
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Website</th>
            <th>Date</th>
            <th>Message</th>
           <!--  <th>Edit</th> -->
        </tr>
    </thead>
    <tbody>
    <?php foreach ($result as $row) : ?>
        <tr>
            <td><?php echo escape($row["id"]); ?></td>
            <td><?php echo escape($row["firstname"]); ?></td>
            <td><?php echo escape($row["lastname"]); ?></td>
            <td><?php echo escape($row["website"]); ?></td>
            <td><?php echo escape($row["date"]); ?></td>
            <td><?php echo escape($row["message"]); ?></td>
           <!--  <td><a href="update-single.php?id=<?php echo escape($row["id"]); ?>">Edit</a></td> -->
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<script>
var target_url = "https://lifeofpablo.com/guestbook/";
var target_url1 = "https://www.disquisitioner.com/posts/post-darkmode/";

// jsonp=f option to disable json return
var count_url   = "https://webmention.io/api/count.json?target=";
var mention_url = "https://webmention.io/api/mentions.jf2?target=";
var mfilter = "&wm-property=in-reply-to";

var defaultphoto = "https://aaronparecki.com/assets/images/no-profile-photo.png"

fetch(count_url+target_url)
  .then(response => {return(response.text());})
  .then(myJson => {showCounts(1,JSON.parse(myJson));});

fetch(mention_url+target_url)
  .then(response => {return(response.text());})
  .then(myJson => {showMentions(JSON.parse(myJson));});

function showCounts(index, data) {
  // Add code to hide mention panel if data.count = 0
  if(data.type.like) { document.getElementById("wm_like"+index).innerHTML = data.type.like; }
  if(data.type.mention) { document.getElementById("wm_ment"+index).innerHTML = data.type.mention; }
  if(data.type.reply) { 
      document.getElementById("wm_reply"+index).innerHTML = 
        pluralize(data.type.reply,"reply","replies");
  }
  if(data.type.repost) { 
    document.getElementById("wm_repost"+index).innerHTML = 
      pluralize(data.type.repost,"repost","reposts"); 
  }
  if(data.type.bookmark) { document.getElementById("wm_bkmk"+index).innerHTML = data.type.bookmark; }
}

function pluralize(num,singular,plural) {
  if(num == 1) return num + " " + singular;
  return num + " " + plural;
}

function showMentions(mentions) {
  var panel = document.getElementById("mentionpanel");
  for(i=0;i<mentions.children.length;i++) {

    var m = mentions.children[i];
    
    var m_div = document.createElement("DIV");
    m_div.className = "webmention";
    panel.appendChild(m_div);

    var m_img = document.createElement("IMG");
    if(m.author.photo) {
      m_img.setAttribute("src",m.author.photo);
    }
    else {
      m_img.setAttribute("src",defaultphoto);
    }
    m_img.setAttribute("width","64");
    m_div.appendChild(m_img);

    var m_info = document.createElement("DIV");
    m_info.className = "wm_info";
    m_div.appendChild(m_info);

    // First line is author's name, liked to author's web site if present
    if(m.author.url) {
      var m_auth = document.createElement("A");
      m_auth.href = m.author.url;
      if(m.author.name) {
        m_auth.innerHTML = m.author.name;
      }
      m_auth.className = "m_author";
      m_info.appendChild(m_auth);

    }
    else {
      if(m.author.name) {
        var m_auth = document.createElement("SPAN");
        m_auth.innerHTML = m.author.name;
        m_auth.className = "m_author";
        m_info.appendChild(m_auth);
      }
    }
    
    // Second line = name of post linked to post URL, though
    // if no name then is just URL
    var m_art = document.createElement("A");
    m_art.href = m.url;
    if(m.name) {
      m_art.innerHTML = m.name;
    }
    else {
      m_art.innerHTML = m.url;
    }
    m_info.appendChild(m_art);

    if(m.published) {
      var d = new Date(m.published);
      var m_pub = document.createElement("TIME");
      m_pub.datetime = m.published;
      m_pub.innerHTML = "Published: " + d.toString();
      m_pub.className = "m_published";
      m_info.appendChild(m_pub);     
    }
  }
}
</script>

<div class="wm_summary">
  <ul class="menicons">
    <li class="micon"><i class="fa-solid fa-star"></i>&nbsp<span id="wm_like1"></span>&nbsp; likes</li>
    <li class="micon"><i class="fa-regular fa-file"></i>&nbsp<span id="wm_ment1"></span>&nbsp; mentions</li>
    <li class="micon"><i class="fa-regular fa-comment"></i>&nbsp<span id="wm_reply1"> </span>&nbsp; replies</li>

    <li class="micon"><i class="fa-solid fa-repeat"></i>&nbsp<span id="wm_repost1"></span>&nbsp; reposts</li>

    <li class="micon"><i class="fa-regular fa-bookmark"></i>&nbsp<span id="wm_bkmk1"></span>&nbsp; bookmarks</li>

</div>
<hr>
<div id="mentionpanel"></div>
<a href="index.php">Back to home</a>

<?php require "templates/footer.php"; ?>
