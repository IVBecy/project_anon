// Dropdown settings menu
$(document).ready(() => {
  if (document.getElementById("menu")){
    const RenderDropDown = () => {
      return (
        <div className="dropdown_menu" id="dropdown_settings" style={{ display: "none", width:"200px" }}>
          <span><a href="./profile.php">Your Profile</a></span>
          <span><a href="./feed.php">Your Feed</a></span>
          <span>Docs</span>
          <hr />
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
          if (elem.style.display == "none") {
            elem.style.display = "block";
            name.style.marginLeft = elem.style.width;
          } else {
            elem.style.display = "none";
          }
        }
      }
    }, 100)
  }
})