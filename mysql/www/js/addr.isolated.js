/**
 * 도서 산간 지역 체크 함수
 * isolatedList 배열의 내용으로 체크
 * @type {{isIsolated: isolatedAddr.isIsolated}}
 */
var isolatedAddr = (function(){

	/**
	 * 도서 산간 지역 리스트
	 * @type {{except: Array, list: *[]}}
	 */
	var isolatedList = {
		'list': [
			{
				'name': '거제시',
				'list': [
					{
						'name': '둔덕면',
						'list': [
							{
								'name': '화도리'
							}
						]
					}
				]
			},
			{
				'name': '사천시',
				'list': [
					{
						'name': '늑도동',
						'list': [
							{
								'name': '신도'
							},
							{
								'name': '마도동'
							}
						]
					},
					{
						'name': '신수동'
					}
				]
			},
			{
				'name': '고흥근',
				'list': [
					{
						'name': '도양읍',
						'list': [
							{
								'name': '득양리'
							},
							{
								'name': '봉암리'
							},
							{
								'name': '상화도'
							},
							{
								'name': '하화도'
							},
							{
								'name': '시신리'
							},
							{
								'name': '지죽리'
							}
						]
					},
					{
						'name': '봉래면',
						'list': [
							{
								'name': '사양리'
							}
						]
					}
				]
			},
			{
				'name': '목포시',
				'list': [
					{
						'name': '달동',
						'list': [
							{
								'name': '외달도'
							},
							{
								'name': '달리도'
							}
						]
					},
					{
						'name': '율도동'
					}
				]
			},
			{
				'name': '보성군',
				'list': [
					{
						'name': '벌교읍',
						'list': [
							{
								'name': '장도리'
							}
						]
					}
				]
			},
			{
				'name': '부산시',
				'list': [
					{
						'name': '강서구',
						'list': [
							{
								'name': '눌차동'
							},
							{
								'name': '대항동'
							},
							{
								'name': '동선동'
							},
							{
								'name': '성북동'
							},
							{
								'name': '천선동'
							}
						]
					}
				]
			},
			{
				'name': '신안군',
				'list': [
					{
						'name': '양해읍',
						'list': [
							{
								'name': '가란리'
							},
							{
								'name': '고이리'
							},
							{
								'name': '매화리'
							}
						]
					},
					{
						'name': '증도면',
						'list': [
							{
								'name': '병풍리'
							}
						]
					},
					{
						'name': '지도읍',
						'list': [
							{
								'name': '선도리'
							},
							{
								'name': '어도리'
							}
						]
					}
				]
			},
			{
				'name': '여수시',
				'list': [
					{
						'name': '경호동'
					},
					{
						'name': '남면'
					},
					{
						'name': '삼사면'
					},
					{
						'name': '화정면',
						'except': [
							{
								'name' : '백야리'
							}
						]
					}
				]
			},
			{
				'name': '영광군',
				'list': [
					{
						'name': '낙월면'
					}
				]
			},
			{
				'name': '완도군',
				'list': [
					{
						'name': '군외면',
						'list': [
							{
								'name': '당인리'
							},
							{
								'name': '불목리'
							},
							{
								'name': '영풍리'
							},
							{
								'name': '황진리'
							}
						]
					},
					{
						'name': '금당면'
					},
					{
						'name': '금일읍'
					},
					{
						'name': '노화읍'
					},
					{
						'name': '보길면'
					},
					{
						'name': '생일면'
					},
					{
						'name': '소안면'
					},
					{
						'name': '청산면'
					}
				]
			},
			{
				'name': '군산시',
				'list': [
					{
						'name': '옥도면'
					}
				]
			},
			{
				'name': '부안군',
				'list': [
					{
						'name': '위도면'
					}
				]
			},
			{
				'name': '당진시',
				'list': [
					{
						'name': '선문면',
						'list': [
							{
								'name': '난리도리'
							}
						]
					},
					{
						'name': '신평면',
						'list': [
							{
								'name': '매산리'
							},
							{
								'name': '행담도'
							}
						]
					}
				]
			},
			{
				'name': '보령시',
				'list': [
					{
						'name': '오천면',
						'list': [
							{
								'name': '고대로리'
							},
							{
								'name': '녹도리'
							},
							{
								'name': '호도'
							},
							{
								'name': '삽시도리'
							},
							{
								'name': '외연도리'
							},
							{
								'name': '원산도리'
							},
							{
								'name': '장고도리'
							},
							{
								'name': '효자도리'
							},
							{
								'name': '소도'
							},
							{
								'name': '월도'
							},
							{
								'name': '육도'
							},
							{
								'name': '추도'
							},
							{
								'name': '허육도'
							}
						]
					}
				]
			},
			{
				'name': '서산시',
				'list': [
					{
						'name': '지곡면',
						'list': [
							{
								'name': '도성리'
							},
							{
								'name': '분점도'
							},
							{
								'name': '우도'
							}
						]
					}
				]
			},
			{
				'name': '태안군',
				'list': [
					{
						'name': '근흥면',
						'list': [
							{
								'name': '가의도리'
							}
						]
					}
				]
			},
			{
				'name': '울릉군',
				'list': [
					{
						'name': '북면'
					},
					{
						'name': '서면'
					},
					{
						'name': '울릉읍'
					}
				]
			},
			{
				'name': '통영시',
				'list': [
					{
						'name': '사량면'
					},
					{
						'name': '산양읍',
						'list': [
							{
								'name': '곤리'
							},
							{
								'name': '연곡리'
							},
							{
								'name': '저림리'
							},
							{
								'name': '추도리'
							}
						]
					},
					{
						'name': '육지면'
					},
					{
						'name': '용남면',
						'list': [
							{
								'name': '어의리'
							},
							{
								'name': '지도리'
							},
							{
								'name': '한산면'
							}
						]
					}
				]
			},
			{
				'name': '강화군',
				'list': [
					{
						'name': '겨동면'
					},
					{
						'name': '산삼면'
					},
					{
						'name': '서도면'
					},
					{
						'name': '옹진군',
						'except': [
							{
								'name': '영흥면'
							}
						]
					},
					{
						'name': '중구',
						'list': [
							{
								'name': '무의동'
							}
						]
					}
				]
			},
			{
				'name': '제주시'
			},
			{
				'name': '서귀포시'
			}
		],
		'except' : []
	};

	/**
	 * 인자 값인 문자열을 isolatedList 와 비교
	 * @param val
	 * @returns {boolean}
	 */
	var isIsolated = function(val){
		var chk = false;
		if(typeof val == "string"){
			if(val.length > 0){
				chk = checkIsolated(isolatedList.list, isolatedList.except, val);
				return chk;
			}else{
				return chk;
			}
		}else{
			return chk;
		}
	};

	var checkIsolated = function(list, except, val){
		var rst = false;

		if(typeof list != "undefined") {
			if (list.length > 0) {
				for (var i = 0; i < list.length; i++) {
					if (val.indexOf(list[i].name) > -1) {
						if(typeof list[i].list != "undefined" || typeof list[i].except != "undefined"){
							rst = checkIsolated(list[i].list, list[i].except, val)
						}else{
							rst = true;
						}
					}
				}
			}
		}
		if(typeof except != "undefined") {
			if(except.length > 0){
				rst = true;
				for(var i = 0; i < except.length ; i++){
					if(val.indexOf(except[i].name) > -1){
						rst = false;
					}
				}
			}
		}

		return rst;
	};

	return {
		isIsolated : function(val){
			var rst = isIsolated(val);
			return rst;
		}
	}

})();