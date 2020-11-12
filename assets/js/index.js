// Get CSRF token
var csrfToken = ("; "+document.cookie).split("; CSRF-Token=").pop().split(";").shift();

// Dropdown settings menu
$(document).ready(() => {
  if (document.getElementById("menu")){
    const RenderDropDown = () => {
      return (
        <div className="dropdown_menu" id="dropdown_settings" style={{ visibility: "hidden", width:"200px" }}>
          <form method="POST" action="../public/profile-src.php">
            <input className="search-usrs" type="search" name="src_name" placeholder="Search" />
          </form>
          <hr/>
          <span><a href="../public/profile">Your Profile</a></span>
          <span><a href="../public/feed">Your Feed</a></span>
          <span id="new_project">New project</span>
          <hr />
          <span><a href="../public/settings">Settings</a></span>
          <span>Docs</span>
          <span><a href="../private/logout.php" style={{color:"red"}}>Sign out</a></span>
        </div> 
      )
    }
    ReactDOM.render(<RenderDropDown />, document.getElementById("menu"))
    setTimeout(() => {
      var name = document.getElementById("dropdown-img");
      var elem = document.getElementById("dropdown_settings");
      if (name) {
        name.onclick = () => {
          if (elem.style.visibility == "hidden") {
            elem.style.visibility = "visible";
          } else {
            elem.style.visibility = "hidden";
          };
        };
      };
    }, 100);
  };
});

// Render the Project post form
const ProjectForm = () => {
  return(
    <div className="center-container">
      <i className="fas fa-times-circle" style={{ fontSize: "30px" }}></i>
      <div className="project-form" id="projects">
        <h2>Post a new project</h2>
        <form method="POST" action="../private/project-gen.php">
          <input type="text" name="title" placeholder="Title of Project" /><br/>
          <textarea name="desc" placeholder="Describe your project"></textarea><br />
          <input type="submit" value="Post" />
          <input type="hidden" name="csrftoken" value={csrfToken}/>
        </form>
      </div>
    </div>
  )
};

// when clicking the "new project" span
$(document).ready(() => {
  var trigger = document.getElementById("new_project");
  var overlay = document.getElementById("project-form-overlay");
  if (trigger) {
    trigger.onclick = () => {
      if (overlay) {
        overlay.style.display = "block";
      }
      setTimeout(() => {ReactDOM.render(<ProjectForm />, overlay)}, 100)
      setTimeout(() => {
        // When clicking the "x" in the overlay
        var x = document.getElementsByClassName("fas fa-times-circle")[0];
        if (x && overlay.style.display == "block") {
          x.onclick = () => {
            overlay.style.display = "none";
          };
        };
      },200)
    };
  };
});

// When you click on the ellipses, you get options regarding your project
const ProjectSettings = () => {
  return(
    <div className="dropdown_menu" id="project-dropdown" style={{visibility:"visible"}}>
      <span id="edit-post">Edit</span>
      <span id="delete-post">Delete</span>
    </div> 
  )
};
const RenderPostEdit = () =>{
  return(
    <div className="center-container" style={{backgroundColor:"white"}}>
      <i className="fas fa-times-circle" style={{ fontSize: "30px" }}></i>
      <div className="edit-post" id="projects">
        <h1>Edit your post</h1>
        <form method="POST" onSubmit={onPostEdit} action="../private/edit-post.php">
          <input type="text" name="title" placeholder="Edit title" /><br />
          <textarea name="desc" placeholder="Edit the description of your projects"></textarea><br />
          <input type="submit" name="send-edited-post" value="Edit Post" />
          <input type="hidden" name="csrftoken" value={csrfToken}/>
        </form>
      </div>
    </div>
  )
};
const DeletePost = () => {
  return (
    <div className="center-container">
      <i className="fas fa-times-circle" style={{ fontSize: "30px" }}></i>
      <div className="delete-post-popup">
        <form method="POST" action="../private/delete-post.php">
          <h4>Are you sure that you want to delete your project?</h4>
          <input type="submit" value="Delete" style={{backgroundColor:"red",color:"white"}}/>
          <input type="hidden" name="csrftoken" value={csrfToken}/>
        </form>
      </div>
    </div>
  )
};

// When we click the edit post button
const onPostEdit = () => {
  var newTitle = document.getElementsByName("title")[0].value;
  var newDesc = document.getElementsByName("desc")[0].value;
  var newProj = {
    "newTitle": newTitle,
    "newDesc": newDesc
  };
  document.cookie = `editedPost=${JSON.stringify(newProj)}; path=/ `
}

$(document).ready(() => {
  var overlay = document.getElementById("project-form-overlay");
  if (document.querySelectorAll(".fas.fa-ellipsis-h")[0]){
    var elips = document.querySelectorAll(".fas.fa-ellipsis-h");
    for (var i in elips){
      if (typeof elips[i] != "object"){}
      else{
        elips[i].onclick = (e) => {
          var elem = document.getElementById("project-dropdown");
          if (elem) {
            ReactDOM.unmountComponentAtNode(e.target)
          } else {
            ReactDOM.render(<ProjectSettings />, e.target)
            //editing posts
            document.getElementById("edit-post").onclick = () => {
              var title = e.target.parentNode.id;
              var projectDesc = e.target.parentNode.getElementsByClassName("project-desc")[0].innerHTML;
              overlay.style.display = "block";
              ReactDOM.render(<RenderPostEdit/>,overlay)
              //set values to the input fields
              setTimeout(() => {
                document.getElementsByName("title")[0].value = title;
                document.getElementsByName("desc")[0].value = projectDesc;
                var oldTitle = document.getElementsByName("title")[0].value;
                var oldDesc = document.getElementsByName("desc")[0].value;
                var oldArray = {
                  "oldTitle": oldTitle,
                  "oldDesc": oldDesc
                };
                document.cookie = `oldPost=${JSON.stringify(oldArray)}; path=/ `
              },100)
              var x = document.getElementsByClassName("fas fa-times-circle")[0];
              if (x && overlay.style.display == "block") {
                x.onclick = () => {
                  overlay.style.display = "none";
                };
              };
            };
            // Post deletion
            document.getElementById("delete-post").onclick = () => {
              overlay.style.display = "block";
              ReactDOM.render(<DeletePost />, overlay)
              var x = document.getElementsByClassName("fas fa-times-circle")[0];
              //set cookie for php top be able to access the item in the array
              document.cookie = `ToBeDeleted=${e.target.parentNode.id} ; path=/ `;
              if (x && overlay.style.display == "block") {
                x.onclick = () => {
                  overlay.style.display = "none";
                };
              };
            }
          };
        };
      };
    };
  };
});