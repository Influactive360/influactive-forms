jQuery(function($) {
  const container = $("#influactive_form_fields_container");

  $("#add_field").click(function(e) {
    e.preventDefault();

    const fieldType = $("#field_type").val();

    let fieldHtml = "<p><label>Type <select class='field_type' name='influactive_form_fields_type[]'>" +
      "<option value='text' " + (fieldType === "text" ? "selected" : "") + ">Text</option>" +
      "<option value='email' " + (fieldType === "email" ? "selected" : "") + ">Email</option>" +
      "<option value='number' " + (fieldType === "number" ? "selected" : "") + ">Number</option>" +
      "<option value='textarea' " + (fieldType === "textarea" ? "selected" : "") + ">Textarea</option>" +
      "<option value='select' " + (fieldType === "select" ? "selected" : "") + ">Select</option>" +
      "<option value='checkbox' " + (fieldType === "checkbox" ? "selected" : "") + ">Checkbox</option>" +
      "</select></label> " +
      "<label>Label <input type='text' name='influactive_form_fields_label[]'></label> " +
      "<label>Name <input type='text' name='influactive_form_fields_name[]'></label> ";

    if (fieldType === "select") {
      fieldHtml += "<a href='#' class='add_option'>Add option</a> " +
        "<div class='options_container'></div>";
    }

    fieldHtml += "<a href='#' class='remove_field'>Remove</a></p>";

    const fieldElement = $(fieldHtml);
    container.append(fieldElement);

    if (fieldType === "select") {
      fieldElement.find(".remove_field").click(removeFieldHandler);
      fieldElement.find(".add_option").click(addOptionHandler);
    }
  });

  container.on("click", ".remove_field", removeFieldHandler);
  container.on("click", ".add_option", addOptionHandler);
  container.on("click", ".remove_option", removeOptionHandler);

  function addOptionHandler(e) {
    e.preventDefault();
    const optionsContainer = $(this).closest("p").find(".options_container");
    const optionHtml = "<p><label>Option <input type='text' name='influactive_form_fields_option[]'></label> <a href='#' class='remove_option'>Remove option</a></p>";
    const optionElement = $(optionHtml);
    optionsContainer.append(optionElement);
    optionElement.find(".remove_option").click(removeOptionHandler);
  }

  function removeFieldHandler(e) {
    e.preventDefault();
    $(this).parent("p").remove();
  }

  function removeOptionHandler(e) {
    e.preventDefault();
    $(this).parent("p").remove();
  }
});
