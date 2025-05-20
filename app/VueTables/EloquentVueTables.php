<?php

namespace App\VueTables;
use Illuminate\Database\Eloquent\Builder;

Class EloquentVueTables implements VueTablesInterface {

	public function get( $model, Array $fields, Array $relations = [],Array $condition = []) {
		$byColumn  = request( 'byColumn' );
		$orderBy  = request( 'orderBy' );
		$limit     = request( 'limit' );
		$page      = request( 'page' );
		$ascending = request( 'ascending' );
		$query     =  request( 'query' );

		$data    = $model->select( $fields )->with($relations);

		$query1 = json_decode($query);
		if($query1!=NULL)
		{
			foreach ($query1 as $k => $val) {
				$string = explode(".", $k);
				if(count($string)==2){
					$data = $data->WhereHas($string[0], function (Builder $q) use($string,$val) {
								$q->where($string[1],'LIKE', "%{$val}%");
							});
				}
			}

		}
		if(!empty($condition)){
			foreach ($condition as $key => $value) {
				// echo request($value);
				if(request($value))
				{
					// echo "existe el filtro";

					$valor = request($value);
					// echo $valor;
					$data = $data->WhereHas($value, function (Builder $q) use($valor) {
						$q->where("id",$valor);
					});
				}else{
					// echo "NOexiste el filtro";
				}
			}
		}else{
			// echo "vacio";

		}
		// $data    = $model->select( $fields )->with($relations);
		// $data    = $model->select( $fields )->with($r);



		if(request('status')) {
			$data->where('status', request('status'));
		}

		if ( isset( $query ) && $query ) {
			$data = $byColumn == 1 ? $this->filterByColumn( $data, $query ) : $this->filter( $data, $query, $fields );
		}
		return $data;
		// $count = $data->count();
		// $data->limit( $limit )->skip( $limit * ( $page - 1 ) );
		// if ( isset( $orderBy )) {
		// 	$direction = $ascending == 1 ? "ASC" : "DESC";
		// 	$data->orderBy( $orderBy, $direction );
		// }else{
		// 	if($order!=''){
		// 		$direction = $by;
		// 		$data->orderBy( $order, $direction );
		// 	}
		// }

		// $results = $data->get()->toArray();

		// return [
		// 	'data'  => $results,
		// 	'count' => $count
		// ];
	}
	function finish($data){

		$orderBy  = request( 'orderBy' );
		$limit     = request( 'limit' );
		$page      = request( 'page' );
		$ascending = request( 'ascending' );

		$count = $data->get()->count();
		$data->limit( $limit )->skip( $limit * ( $page - 1 ) );
		if ( isset( $orderBy )) {
			$direction = $ascending == 1 ? "ASC" : "DESC";
			$data->orderBy( $orderBy, $direction );
		}
		// else{
		// 	if($order!=''){
		// 		$direction = $by;
		// 		$data->orderBy( $order, $direction );
		// 	}
		// }

		$results = $data->get()->toArray();
        // dd($data->get());
		return [
			'data'  => $results,
			'count' => $count
		];

	}

	protected function filterByColumn( $data, $query ) {
		$query1 = json_decode($query);
		foreach ( $query1 as $field => $val ) {
			$string = explode(".", $field);
			if(count($string)==1){
				// if ( ! $val ) {
				// 	continue;
				// }
				// if ( is_string( $val ) && $field !== "status" ) {
					// var_dump(is_string( $val ));
				if ( is_string( $val )) {
					// echo "string<br>$val";
					$data->where( $field, 'LIKE', "%{$val}%" );
				}else{
					// echo "numero<br>$val";
					$data->where( $field, $val);
				}
			}
		}

		return $data;
	}

	protected function filter( $data, $query, $fields ) {
		foreach ( $fields as $index => $field ) {
			$method = $index ? "orWhere" : "where";
			$data->{$method}( $field, 'LIKE', "%{$query}%" );
		}

		return $data;
	}
	// public function whereEloquent($data,$where){
	// 	$data->$where;
	// }
}
