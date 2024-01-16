var fileLocation = $("script[src*=script]")
  .attr("src")
  .replace(/script\.js.*$/, "");

// Uploaded image array from OS&D form
var pictures = {};

/*
 * Insert image source below the camera/file input for users to see what they have uploaded.
 * Add image source to array of pictures
 */
function displayImages(files) {
  if (files && files[0]) {
    for (let i = 0; i < files.length; i++) {
      let reader = new FileReader();

      reader.onload = function (e) {
        var fileName = files.item(i).name;
        pictures[fileName] = e.target.result;
        $("#selectedFiles").html("");
        $("#selectedFiles").append(
          '<div class="col-md-4 p-2 container-img" id="' +
            fileName +
            '"><img src="' +
            e.target.result +
            '" width="75" height="75"><i class="bi bi-trash-fill removePhoto" id="' +
            fileName +
            '"></i></div>'
        );
      };

      reader.readAsDataURL(files[i]);
    }
  }
}

/*
 * Document is ready and custom hooks can be performed
 */
$(document).ready(function () {
  // Removes pictures from the OS&D and Accident Report form image input when delete icon is clicked
  $(document).on("click", ".removePhoto", function () {
    $(this).parent().remove();

    pictures[$(this).attr("id")] = undefined;
  });

  $("#navSearchButton").click(function () {
    if ($(".navbarSearchDesktop").is(":hidden")) {
      $(".navbarSearchDesktop").show("slideDown");
    } else {
      if ($("#navSearchFieldDesktop").val().length > 0) {
        window.location.replace('/projects/recipe-book/search?query=' + $("#navSearchFieldDesktop").val());
      } else {
        $(".navbarSearchDesktop").hide("slideUp");
      }
    }
  });

  // Removes the invalid styling from the camera input
  $("#cameraInput").change(function () {
    $("#cameraInput").removeClass("is-invalid");
  });

  // Removes the invalid styling from the specified inputs
  $("#name, #source, #ingredients, #instructions, #course").change(function () {
    if ($(this).val().length > 0) {
      $(this).removeClass("is-invalid");
      $(this).addClass("is-valid");
    }
  });

  $("#clearFormButton").click(function () {
    window.scrollTo(0, 0);

    // Clear all fields, probably a much better way to do this
    var fields = [
      "name",
      "source",
      "ingredients",
      "instructions",
      "notes",
      "nf-calories",
      "nf-carbohydrates",
      "nf-protein",
      "nf-fat",
      "nf-fiber",
      "nf-sugar",
      "nf-sodium",
      "nf-cholesterol",
      "servings",
      "prepTime",
      "cookTime",
      "course",
    ];

    fields.forEach(function (field) {
      var f = $("#" + field);
      f.val("");
      f.removeClass("is-valid");
      f.removeClass("is-invalid");
    });

    // Clear pictures
    pictures = {};
    $("#selectedFiles").html("");
  });

  // Submission of the OS&D Form
  $("#addRecipeForm").submit(function (e) {
    e.preventDefault();
    var form = $(this);

    var valid = validateForm();

    if (!valid) {
      $("#response").html(
        '<span class="text-danger">You must complete all required fields.</span>'
      );
      e.stopPropagation();
      return;
    }

    var formData = form.serializeArray();
    formData.push({ name: "date", value: new Date().toISOString() });
    formData.push({ name: "pictures", value: JSON.stringify(pictures) });

    // encode ingredients and instructions
    formData[2]["value"] = encodeURIComponent(formData[2]["value"]);
    formData[3]["value"] = encodeURIComponent(formData[3]["value"]);
    formData[4]["value"] = encodeURIComponent(formData[4]["value"]);

    $.ajax({
      type: "POST",
      url: form.attr("action"),
      data: formData,
    }).done(function (data) {
      if (data === "ok") {
        $("#response").html(
          '<span class="text-success">Recipe successfully added!</span>'
        );
      } else {
        $("#response").html(
          '<span class="text-danger">Recipe upload failed!</span>'
        );
      }
      console.log(data);
    });
  });

  /*
   * Animated dropdown menus
   */
  $(".dropdown-menu").addClass("invisible");

  $(".dropdown").on("show.bs.dropdown", function (e) {
    $(".dropdown-menu").removeClass("invisible");
    $(this).find(".dropdown-menu").first().stop(true, true).slideDown();
  });

  $(".dropdown").on("hide.bs.dropdown", function (e) {
    $(this).find(".dropdown-menu").first().stop(true, true).hide();
  });
});

function validateForm() {
  var valid = true;
  const fields = ["name", "source", "ingredients", "instructions", "course"];

  for (var i = 0; i < fields.length; i++) {
    if (!isValid(fields[i])) {
      valid = false;
    }
  }

  if ($("#cameraInput").get(0).files.length === 0) {
    $("#cameraInput").addClass("is-invalid");
    valid = false;
  }

  return valid;
}

/*
 * Compressing and processing pictures from OS&D tool
 */
const compressImage = async (file, { quality = 1, type = file.type }) => {
  const imageBitmap = await createImageBitmap(file);

  const canvas = document.createElement("canvas");
  canvas.width = imageBitmap.width;
  canvas.height = imageBitmap.height;
  canvas.getContext("2d").drawImage(imageBitmap, 0, 0);

  const blob = await new Promise((resolve) =>
    canvas.toBlob(resolve, type, quality)
  );

  return new File([blob], file.name, {
    type: blob.type,
  });
};

// Get the selected file from the file input
const input = document.querySelector("#cameraInput");
if (input) {
  input.addEventListener("change", async (e) => {
    const { files } = e.target;

    if (!files.length) return;

    const dataTransfer = new DataTransfer();

    // For every file in the files list, skipping non images
    for (const file of files) {
      if (!file.type.startsWith("image")) {
        dataTransfer.items.add(file);
        continue;
      }

      // Compress the file by 50%
      const compressedFile = await compressImage(file, {
        quality: 0.5,
        type: "image/jpeg",
      });

      dataTransfer.items.add(compressedFile);
    }

    // Set value of the file input to our new files list
    e.target.files = dataTransfer.files;

    // Display the images and compile array to send with the form data
    displayImages(e.target.files);
  });
}

/*
 * Dark / Light mode functionality
 */
function toggleDarkMode() {
  let theme = localStorage.getItem("theme");
  if (theme === "dark") {
    document.documentElement.setAttribute("data-theme", "light");
    localStorage.setItem("theme", "light");
    if (document.getElementById("darkModeSwitch"))
      document.getElementById("darkModeSwitch").checked = false;
  } else if (theme === "light") {
    document.documentElement.setAttribute("data-theme", "dark");
    localStorage.setItem("theme", "dark");
    if (document.getElementById("darkModeSwitch"))
      document.getElementById("darkModeSwitch").checked = true;
  }
}

let theme = localStorage.getItem("theme");
if (!theme || theme === "light") {
  document.documentElement.setAttribute("data-theme", "light");
  if (document.getElementById("darkModeSwitch"))
    document.getElementById("darkModeSwitch").checked = false;
  localStorage.setItem("theme", "light");
} else if (theme === "dark") {
  document.documentElement.setAttribute("data-theme", "dark");
  if (document.getElementById("darkModeSwitch"))
    document.getElementById("darkModeSwitch").checked = true;
  localStorage.setItem("theme", "dark");
}

//TODO: Rewrite this function
document.addEventListener("DOMContentLoaded", function (event) {
  const showNavbar = (
    toggleId,
    mobileToggleId,
    navId,
    bodyId,
    headerId,
    footerId
  ) => {
    const toggle = document.getElementById(toggleId),
      mobileToggle = document.getElementById(mobileToggleId),
      nav = document.getElementById(navId),
      bodypd = document.getElementById(bodyId),
      headerpd = document.getElementById(headerId),
      footerpd = document.getElementById(footerId);

    if (toggle && mobileToggle && nav && bodypd && headerpd) {
      if (
        $(window).width() >= 768 &&
        (!localStorage.getItem("showSidebar") ||
          localStorage.getItem("showSidebar") === "true")
      ) {
        nav.classList.toggle("show");
        //toggle.classList.toggle("bx-x");
        bodypd.classList.toggle("body-pd");
        headerpd.classList.toggle("body-pd");
        footerpd.classList.toggle("body-pd");
        localStorage.setItem("showSidebar", "true");
      }
      if ($(window).width() < 768) {
        mobileToggle.classList.toggle("show");
      }

      mobileToggle.addEventListener("click", () => {
        //mobileToggle.classList.toggle("show");
        nav.classList.toggle("show");
        bodypd.classList.toggle("body-pd");
        // add padding to header
        headerpd.classList.toggle("body-pd");
        footerpd.classList.toggle("body-pd");
      });

      toggle.addEventListener("click", () => {
        // show navbar
        nav.classList.toggle("show");
        mobileToggle.classList.toggle("show");
        // change icon
        //toggle.classList.toggle("bx-x");
        // add padding to body
        bodypd.classList.toggle("body-pd");
        // add padding to header
        headerpd.classList.toggle("body-pd");
        if (footerpd) footerpd.classList.toggle("body-pd");
        var showSidebar = localStorage.getItem("showSidebar");
        localStorage.setItem(
          "showSidebar",
          showSidebar === "true" ? "false" : "true"
        );
      });
    }
  };

  showNavbar(
    "header-toggle",
    "header-toggle-mobile",
    "nav-bar",
    "body-pd",
    "header",
    "footer"
  );

  // Style the active link
  const linkColor = document.querySelectorAll(".nav_link");

  function colorLink() {
    if (linkColor && !this.classList.contains("submenu")) {
      linkColor.forEach((l) => l.classList.remove("active"));
      this.classList.add("active");
    }
  }
  linkColor.forEach((l) => l.addEventListener("click", colorLink));
});

/*
 * Used to validate fields in forms, checking if they are not empty and contain at least @length characters.
 */
function isValid(id, length = 1, maxLength = 0) {
  var input = $("#" + id);
  var valid = true;

  if (!input) return false;

  if (
    !input.val() ||
    input.val() === "" ||
    input.val().length < length ||
    (maxLength > 0 && input.val().length > maxLength)
  ) {
    document.querySelector("#" + id).classList.add("is-invalid");
    valid = false;
  } else {
    document.querySelector("#" + id).classList.remove("is-invalid");
  }
  return valid;
}
