(function($)
{
	// This script was written by Steve Fenton
	// http://www.stevefenton.co.uk/Content/Jquery-Mobile-Drag-And-Drop/
	// Feel free to use this jQuery Plugin
	// Version: 3.0.2
    // Contributions by: 
	
	// Holds the current item being dragged
	var currentDrag = "";
	var ruleChecked = false;
	var dragCounter = 0;
	
	// Current mouse position (at all times!)
	var mouseX = 0;
	var mouseY = 0;
	
	// May be used to adjust offset of the ghost item
	var liftX = 0;
	var liftY = 0;
	
	$(document).mousemove(function(e){
		mouseX = e.pageX;
		mouseY = e.pageY;
		$("#ghost").css({ top: (mouseY-liftY)+"px", left: (mouseX-liftX)+"px" });
	}); 

	function IsValidDrop(id) {
		var returnValue = false;
		var currentDragParent = "#" + $(currentDrag).parent().attr("id");
		if (currentDragParent == id) {
			if (ruleChecked) {
				returnValue = true;
			} else {
				returnValue = false;
			}
		} else {
			returnValue = true;
		}
		// Prevents the rule from failing on the same item twice in a row
		ruleChecked = true;
		
		return returnValue;
	}

	function ProcessDragEvent(id, dragSelector, dropSelector, statusSelector, selectedClass, activeClass) {
		
		// Set the currentDrag item
		currentDrag = id;
		ruleChecked = false;
		
		// Remove the selected class from all draggable items 
		// and add it to the current item
		$(dragSelector).removeClass(selectedClass);
		$(currentDrag).addClass(selectedClass);
		
		$("#ghost").remove();
		$(currentDrag).clone().attr("id", "ghost").css({ position: "absolute" }).appendTo("body").fadeTo(0, 0.5);
		
		// Outputs the current draggable item onto the page
		if (statusSelector.length > 0) {
			$(statusSelector).find("#dragging").html(currentDrag);
		}
		
		// If the current draggable item isn't blank, highlight 
		// the droppable zones
		if (currentDrag.length > 1) {
			$(dropSelector).addClass(activeClass);
		}
	}
	
	function ProcessDropEvent(id, dragSelector, dropSelector, statusSelector, selectedClass, activeClass) {
	
		if (IsValidDrop(id) && $(id).hasClass(activeClass)) {
			// If the current draggable item isn't blank, we will
			// move the element and place it inside the target 
			// droppable element
			if (currentDrag.length > 1) {
				$("#ghost").remove();
				$(currentDrag).removeClass(selectedClass);
				$(currentDrag).remove().prependTo(id);
				ProcessDragEvent("", dragSelector, dropSelector, statusSelector, selectedClass, activeClass);
				if (statusSelector.length > 0) {
					$(statusSelector).find("#dropping").html(id);
				}
			}
			
			// Remove the highlighting of droppable zones
			$(dropSelector).removeClass(activeClass);
		}
	}
	
	$.fn.mobiledraganddrop = function (settings) {
	
		var config = {
			classmodifier: "mobiledraganddrop",
			targets: ".drop",
			status: "",
			selectedclass: "selected",
			activeclass: "active"
		};
		
		dragCounter++;
		var selectorForDropZones = "";
		var selectorForDraggables = "";
		var selectorForStatus = "";
		var selectedClass = "";
		var activeClass = "";

		if (settings) {
			$.extend(config, settings);
		}

		return this.each(function () {
			
			selectedClass = config.selectedclass;
			activeClass = config.activeclass;
			
			selectorForDropZones = "." + config.classmodifier + dragCounter + "drop";
			$(config.targets).addClass(config.classmodifier + dragCounter + "drop");
			
			selectorForDraggables = "." + config.classmodifier + dragCounter + "drag";
			$(this).addClass(config.classmodifier + dragCounter + "drag");
			
			selectorForStatus = config.status;
			if (selectorForStatus.length > 0) {
				$(selectorForStatus).html('<p>Dragging: <span id="dragging">&nbsp;</span><br>Dropping: <span id="dropping">&nbsp;</span></p>');
			}
			
			// Adds drag events to draggable items
			$(selectorForDraggables).unbind("mousedown");
            $(selectorForDraggables).unbind("touchstart");
			$(selectorForDraggables).live("mousedown touchstart", function() {
				var id = "#" + $(this).attr("id");
				ProcessDragEvent(id, selectorForDraggables, selectorForDropZones, selectorForStatus, selectedClass, activeClass);
				return false;
			});
			
			// Event handler for drop zones
			$(selectorForDropZones).live("mouseup touchend", function () {
				var id = "#" + $(this).attr("id");
				ProcessDropEvent(id, selectorForDraggables, selectorForDropZones, selectorForStatus, selectedClass, activeClass);
				return false;
			});
		});

	};
})(jQuery);