// Dropdown settings menu
$(document).ready(() => {
  if (document.getElementById("menu")){
    const RenderDropDown = () => {
      return (
        <div className="dropdown_menu" id="dropdown_settings" style={{ visibility: "hidden", width:"200px" }}>
          <span><a href="./profile.php">Your Profile</a></span>
          <span><a href="./feed.php">Your Feed</a></span>
          <span id="new_project">New project</span>
          <hr />
          <span>Docs</span>
          <span><a href="./logout.php">Sign out</a></span>
        </div> 
      )
    }
    ReactDOM.render(<RenderDropDown />, document.getElementById("menu"))
    setTimeout(() => {
      var name = document.getElementById("uname");
      var elem = document.getElementById("dropdown_settings");
      if (name) {
        name.onclick = () => {
          if (elem.style.visibility == "hidden") {
            elem.style.visibility = "visible";
          } else {
            elem.style.visibility = "hidden";
          }
        }
      }
    }, 100)
  }
})

// Render the Project post form
const ProjectForm = () => {
  return(
    <div className="center-container" id="background">
      <div className="project-form">
        <i className="fas fa-times-circle" style={{ fontSize: "30px", }}></i>
        <h1>Post a new project</h1>
        <form method="POST" action="../../php/project-gen.php">
          <input type="text" name="title" placeholder="Title of Project" /><br/>
          <input type="text" name="desc" placeholder="Description of your project" /><br />
          <input type="submit" value="Post" />
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
          }
        }
      },200)
    };
  };
})