


(function( window, undefined ) {
	window.mockServiceProxy = {
		objects: {
			person: {
				// 
				name: '',
				// foo
				age: 0
			}
		},
		operations: {
			addNumbers: function(numberOne, numberTwo) {
				this.data = {
					endPoint: '/robert/modules/Rad.Services/services/mockServiceJSON.php',
					arguments: {
						numberOne: numberOne,
						numberTwo: numberTwo
					},
					complete: false,
					pending: false,
					result: null,
					exception: null,
					errors: [],
					messages: []
				};
				this.execute = function(callBack) {
					this.callBack = callBack;
					var me = this;
					$.ajax({
						//url: this.data.endPoint + '/Rad.Services.RPC/serve/addNumbers/' + escape(this.data.arguments.numberOne) + '/' + this.data.arguments.numberTwo,
						url: this.data.endPoint + '/Rad.Services.RPC/serve/addNumbers',
						data: {numberOne: this.data.arguments.numberOne, numberTwo: this.data.arguments.numberTwo},
						type: 'POST',
						success: function(data) {
							me.data.success = true;
							me.data.result = data.value;
							me.data.exception = data.exception;
							me.data.messages = data.messages;
							me.callBack(me);
						}
					});
					return this;
				};
				return this;
			}
		}
	};
})(window);
