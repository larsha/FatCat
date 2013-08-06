import View\Admin\Index

{{REPLACE body}}
	This is the data view
	<p>
		<a href="new" class="btn btn-default">New</a>
	</p>
	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th></th>
				{{FOR headers AS header}}
					<th>{{header}}</th>
				{{ENDFOR}}
			</tr>
		</thead>
		<tbody>
			{{FOR data AS row}}
				<tr>
					<td><a href="{{row.id}}">Edit</a></td>
					{{FOR row AS value}}
						<td>{{value}}</td>
					{{ENDFOR}}
				</tr>
			{{ENDFOR}}
		</tbody>
	</table>
{{ENDREPLACE}}