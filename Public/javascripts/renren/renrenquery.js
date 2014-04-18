$(function(){
	var provinceSelect = $("#provincename");
	$.post("/renren/getProvinces", {}, function(data){
		for(var i=0; i<data.data.length; i++){
			var provinceOption = "<option value="+ data.data[i]['PROID'] +">"+ data.data[i]['PRONAME'] +"</option>";
			provinceSelect.append(provinceOption);
		}
	}, 'json');

	$("#query").click(function(){
		var provinceid = $('#provincename option:selected').val();
		var collegeid = $('#collegename option:selected').val();
		var departmentid = $('#departmentname option:selected').val()
		var queryResult = $("#queryresult");
		$.post("/renren/queryTotal", {
			'pid': provinceid,
			'cid': collegeid,
			'did': departmentid
		}, function(data){
			queryResult.text(data.data);
		}, 'json');
	});
});

function selectProvince(){
	var provinceid = $('#provincename option:selected').val();
	var collegeSelect = $("#collegename");
	collegeSelect.empty();
	$.post("/renren/getCollege", {
		'pid': provinceid
	}, function(data){
		for(var i=0; i<data.data.length; i++){
			var collegeOption = "<option value="+ data.data[i]['COLID'] +">"+ data.data[i]['COLNAME'] +"</option>";
			collegeSelect.append(collegeOption);
		}
	}, 'json');
}

function selectDepartment(){
	var collegeid = $('#collegename option:selected').val();
	var departmentSelect = $("#departmentname");
	departmentSelect.empty();
	$.post("/renren/getDepartment", {
		'cid': collegeid
	}, function(data){
		for(var i=0; i<data.data.length; i++){
			var departmentOption = "<option value="+ data.data[i]['id'] +">"+ data.data[i]['DEPNAME'] +"</option>";
			departmentSelect.append(departmentOption);
		}
	}, 'json');
}