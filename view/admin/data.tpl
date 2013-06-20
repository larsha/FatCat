import View\Admin\Index

{{REPLACE body}}
	This is the data view
	<table>
		<tr>
			{{FOR headers AS header}}
				<th>{{header}}</th>
			{{ENDFOR}}
		</tr>
		{{FOR data AS row}}
			<tr>
				{{FOR row AS value}}
					<td>{{value}}</td>
				{{ENDFOR}}
			</tr>
		{{ENDFOR}}
	</table>
{{ENDREPLACE}}