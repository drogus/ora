var TaskManagement = function()
{
	this.bindEventsOn();
};

TaskManagement.prototype = {

	constructor: TaskManagement,
	classe: 'TaskManagement',
	
	statuses: {
			0: 'Idea',
			10:	'Open',
			20: 'Ongoing',
			30: 'Completed',
			40: 'Accepted'
	},
	
	bindEventsOn: function()
	{
		var that = this;
	        
		$("#createTaskModal").on("submit", "form", function(e){
			e.preventDefault();
			that.createNewTask(e);
		});
		
		$("#editTaskModal").on("show.bs.modal", function(e) {
			var button = $(e.relatedTarget) // Button that triggered the modal
			var url = button.data('href');
			var subject = button.data('subject');
			$("#editTaskModal form").attr("action", url);
			$('#editTaskSubject').val(subject);
		});

		$("#editTaskModal").on("submit", "form", function(e){
			e.preventDefault();
			that.editTask(e);
		});
		
		// DELETE TASK
		$("body").on("click", "a[data-action='deleteTask']", function(e){
			e.preventDefault();
			that.deleteTask(e);
		});
		
		// JOIN TASK MEMBERS
		$("body").on("click", "a[data-action='joinTask']", function(e){
			e.preventDefault();
			that.joinTask(e);
		});
		
		// UNJOIN TASK MEMBERS
		$("body").on("click", "a[data-action='unjoinTask']", function(e){
			e.preventDefault();
			that.unjoinTask(e);
		});

        //ACCEPT TASK FOR KAMBANIZE       
		$("body").on("click", "button[data-action='acceptTask']", function(e){
			e.preventDefault();
			that.acceptTask(e);
		});

		$("body").on("click", "button[data-action='completeTask']", function(e){
			e.preventDefault();
			that.completeTask(e);
		});

        //BACK TO ONGOING             
		$("body").on("click", "button[data-action='executeTask']", function(e){
			e.preventDefault();
			that.executeTask(e);
		});

		$("#estimateTaskModal").on("show.bs.modal", function(e) {
			var button = $(e.relatedTarget) // Button that triggered the modal
			var url = button.data('href');
			var credits = button.data('credits');
			$("#estimateTaskModal form").attr("action", url);
			if(credits == -1) {
				$('#estimateTaskCredits').val(null);
			    $("#estimateTaskCredits").prop('disabled', true);
			    $("#estimateTaskSkip").prop('checked', true);
			} else {
				$('#estimateTaskCredits').val(credits);
			}
		});
		
		$('#estimateTaskSkip').on('click', function(e) {
			if(this.is(':checked')) {
			    $("#estimateTaskCredits").prop('disabled', true);
			} else {
				$("#estimateTaskCredits").prop('disabled', false);
			}
		});

		//INSERT ESTIMATION
		$("#estimateTaskModal").on("submit", "form", function(e){
			e.preventDefault();
			that.estimateTask(e);
		});
	},
	
	unjoinTask: function(e)
	{
		var url = $(e.target).attr('href');
		
		that = this;
		
		$.ajax({
			url: url,
			method: 'DELETE',
			dataType: 'json',
			complete: function(xhr, textStatus) {
				alertDiv = $('#tasks-alert');
				alertDiv.removeClass();
				if (xhr.status === 200) {
					alertDiv.addClass('alert alert-success');
					alertDiv.text('You successfully left the team that is working on the task');
					that.listTasks();
				}
				else if (xhr.status === 204) {
					alertDiv.addClass('alert alert-warning');
					alertDiv.text('You are not member of the team that is working on the task');
				}
				else {
					alertDiv.addClass('alert alert-danger');
					alertDiv.text('An unknown error "' + xhr.status + '" occurred while trying to leave the task');
				}
			}
		});
	},
	
	joinTask: function(e)
	{
		var url = $(e.target).attr('href');
		
		that = this;
		
		$.ajax({
			url: url,
			method: 'POST',
			dataType: 'json',
			complete: function(xhr, textStatus) {
				alertDiv = $('#tasks-alert');
				alertDiv.removeClass();
				if (xhr.status === 201) {
					alertDiv.addClass('alert alert-success');
					alertDiv.text('You successfully joined the team that is working on the task');
					that.listTasks();
				}
				else if (xhr.status === 204) {
					alertDiv.addClass('alert alert-warning');
					alertDiv.text('You are already member of the team that is working on the task');
				}
				else {
					alertDiv.addClass('alert alert-danger');
					alertDiv.text('An unknown error "' + xhr.status + '" occurred while trying to join the task');
				}
			}
		});
	},
	
	getTask: function(e)
	{
		var url = $(e.relatedTarget).attr('href');
		$.ajax({
			url: url,
			method: 'GET',
			dataType: 'json'
		})
		.done(this.onTaskCompleted.bind(this));
		
	},
	
	onTaskCompleted: function(json) {
	},
	
	editTask: function(e)
	{
		var url = $(e.target).attr('action');

		that = this;
		
		$.ajax({
			url: url,
			method: 'PUT',
			data: $('#editTaskModal form').serialize(),
			dataType: 'json',
			complete: function(xhr, textStatus) {
				if (xhr.status === 202) {
					that.listTasks();
					$('#editTaskModal').modal('hide');
				}
				else {
					alertDiv = $('#editTaskModal div.alert');
					alertDiv.removeClass();
					alertDiv.addClass('alert alert-danger');
					alertDiv.text('An unknown error "' + xhr.status + '" occurred while trying to edit the task');
				}
			}
		});
	},
	
	deleteTask: function(e)
	{
		if (!confirm('Are you sure you want to delete this task?')) {
			return;
		}

		var url = $(e.target).attr('href');
			
		that = this;
		
		$.ajax({
			url: url,
			method: 'DELETE',
			dataType: 'json',
			complete: function(xhr, textStatus) {
				alertDiv = $('#tasks-alert');
				alertDiv.removeClass();
				if (xhr.status === 200) {
					alertDiv.addClass('alert alert-success');
					alertDiv.text('You successfully deleted the task');
					that.listTasks();
				}
				else {
					alertDiv.addClass('alert alert-danger');
					alertDiv.text('An unknown error "' + xhr.status + '" occurred while trying to delete the task');
				}
			}
		});
	},
	
	acceptTask: function(e){
		var url = $(e.target).attr('href');
		
		that = this;
		
        $.ajax({
            url: url,
            method: 'POST',
            data:{action:'accept'},
            dataType: 'json',
            complete: function(xhr, textStatus) {
				alertDiv = $('#tasks-alert');
				alertDiv.removeClass();
				if (xhr.status === 200) {
					alertDiv.addClass('alert alert-success');
					alertDiv.text('You have successfully accepted the task');
					that.listTasks();
				}
				else if (xhr.status === 204) {
					alertDiv.addClass('alert alert-warning');
					alertDiv.text('The task is already accepted');
				}
				else {
					alertDiv.addClass('alert alert-danger');
					alertDiv.text('An unknown error "' + xhr.status + '" occurred while trying to acceot the task');
				}
            }
        });
    },

	completeTask: function(e){
		var url = $(e.target).attr('href');
		
		that = this;
		
        $.ajax({
            url: url,
            method: 'POST',
            data:{action:'complete'},
            dataType: 'json',
            complete: function(xhr, textStatus) {
				alertDiv = $('#tasks-alert');
				alertDiv.removeClass();
				if (xhr.status === 200) {
					alertDiv.addClass('alert alert-success');
					alertDiv.text('You have successfully completed the task');
					that.listTasks();
				}
				else if (xhr.status === 204) {
					alertDiv.addClass('alert alert-warning');
					alertDiv.text('The task is already completed');
				}
				else {
					alertDiv.addClass('alert alert-danger');
					alertDiv.text('An unknown error "' + xhr.status + '" occurred while trying to complete the task');
				}
            }
        });
    },

    executeTask: function(e){
		var url = $(e.target).attr('href');
		
		that = this;
		
        $.ajax({
            url: url,
            method: 'POST',
            data:{action:'execute'},
            dataType: 'json',
            complete: function(xhr, textStatus) {
				alertDiv = $('#tasks-alert');
				alertDiv.removeClass();
				if (xhr.status === 200) {
					alertDiv.addClass('alert alert-success');
					alertDiv.text('You have successfully put in execution the task');
					that.listTasks();
				}
				else if (xhr.status === 204) {
					alertDiv.addClass('alert alert-warning');
					alertDiv.text('The task is already in execution');
				}
				else {
					alertDiv.addClass('alert alert-danger');
					alertDiv.text('An unknown error "' + xhr.status + '" occurred while trying to execute the task');
				}
            }
        });
    },

    listTasks: function()
	{
		$.ajax({
			url: '/task-management/tasks',
			method: 'GET',
			dataType: 'json'
		})
		.done(this.onListTasksCompleted.bind(this));
	},
	
	onListTasksCompleted: function(json)
	{
		var container = $('#tasks');
		container.empty();
		
		if ($(json.tasks).length == 0) {
			container.append("<tr><td colspan='6'>No available tasks found</td></tr>");
		} else {
			that = this;
			if(json._links.create != undefined) {
				$("#createTaskModal form").attr("action", json._links.create);
				$("#createTaskBtn").show();
			} else {
				$("#createTaskModal form").attr("action", null);
				$("#createTaskBtn").hide();
			}
			$.each(json.tasks, function(key, task) {
				subject = task._links.self == undefined ? task.subject : '<a href="' + task._links.self + '">' + task.subject + '</a>';
				createdAt = new Date(Date.parse(task.createdAt));
				var actions = [];
				if (task._links.edit != undefined) {
					actions.push('<a data-href="' + task._links.edit + '" data-subject="' + task.subject + '" data-toggle="modal" data-target="#editTaskModal" class="btn btn-default">Edit</a>');
				}
				if (task._links.join != undefined) {
					actions.push('<a href="' + task._links.join + '" class="btn btn-default" data-action="joinTask">Join</a>');
				}
				if (task._links.unjoin != undefined) {
					actions.push('<a href="' + task._links.unjoin + '" data-action="unjoinTask" class="btn btn-default">Unjoin</a>');
				}
				if (task._links['delete'] != undefined) {
					actions.push('<a href="' + task._links['delete'] + '" data-action="deleteTask" class="btn btn-default">Delete</a>');
				}
				if (task._links.execute != undefined) {
					actions.push('<button data-action="executeTask" class="btn btn-default">Ongoing</button>');
				}
				if (task._links.estimate != undefined) {
					s = '<a data-href="' + task._links.estimate + '"';;
					if(task.estimation != null) {
						s += ' data-credits="' + task.estimation + '"';
					}
					s += ' data-toggle="modal" data-target="#estimateTaskModal" class="btn btn-default">Estimate</a>';
					actions.push(s);
				}
				if (task._links.complete != undefined) {
					actions.push('<button data-action="completeTask" class="btn btn-default">Complete</button>');
				}
				if (task._links.accept != undefined) {
					actions.push('<button data-action="acceptTask" class="btn btn-default">Accept</button>');
				}
				if (task._links.assignShares != undefined) {
					actions.push('<button class="btn btn-default">Assign share</button>');
				}
				switch(task.estimation) {
				case undefined:
					estimation = '';
					break;
				case -1:
					estimation = '<li>Estimation skipped</li>';
					break;
				case null:
					estimation = '<li>Estimation in progress</li>';
					break;
				default:
					estimation = '<li>' + task.estimation + ' credits</li>';
				}
				
				a = actions.length == 0 ? '' : '<li>' + actions.join(' ') + '</li>';

				container.append(
                    '<li class="panel panel-default">' +
						'<div class="panel-heading">' + subject + '</div>' +
						'<div class="panel-body"><ul><li>Created at ' + createdAt.toLocaleString() + "</li>" +
						'<li>' + that.statuses[task.status] + '</li>' +
						estimation +
						"<li>Members: " + $.map(task.members, function(object, key) {
							rv = '<span class="task-member">' + object.firstname + " " + object.lastname;
							if(object.estimation != null){
								rv += ' <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>';
							}
							return rv + '</span>';
						}).join('') + "</li>" + 
						a + '</div>' +
					'</li>');
			});
		}
	},
	
	createNewTask: function(e)
	{
		var url = $(e.target).attr('action');

		that = this;
		
		$.ajax({
			url: url,
			method: 'POST',
			data: $('#createTaskModal form').serialize(),
			dataType: 'json',
			complete: function(xhr, textStatus) {
				if (xhr.status === 201) {
					that.listTasks();
					$('#createTaskModal').modal('hide');
				}
				else {
					alertDiv = $('#createTaskModal div.alert');
					alertDiv.removeClass();
					alertDiv.addClass('alert alert-danger');
					alertDiv.text('An unknown error "' + xhr.status + '" occurred while trying to create the task');
				}
			}
		});
	},
	
	estimateTask : function (e){
		var url = $(e.target).attr('action');

		that = this;
		
		var credits = $('#estimateTaskSkip').is(':checked') ? -1 : $("#estimateTaskCredits").val();
				
		$.ajax({
			url: url,
			method: 'POST',
			data: {value:credits},
			dataType: 'json',
			complete: function(xhr, textStatus) {
				if (xhr.status === 201) {
					that.listTasks();
					$('#estimateTaskModal').modal('hide');
				} else {
					alertDiv = $('#estimateTaskModal div.alert');
					alertDiv.removeClass();
					alertDiv.addClass('alert alert-danger');
					alertDiv.text('An unknown error "' + xhr.status + '" occurred while trying to estimate the task');
				}
			}
		});
	}
	
};

$().ready(function(e){
	collaboration = new TaskManagement();
	collaboration.listTasks();
});