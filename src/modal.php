<?php
    namespace Gateway;

    class Modal {
        private $options = [
            'backdrop'=> true,
            'on_backdrop_click'=> true,

            'modal_zindex'=> 10500,
            'modal_width'=> '90vw',
            'modal_height'=> '90vh',
            'modal_position_top'=> '50%',
            'modal_position_left'=> '50%',
            'modal_background_color'=> '#ffffff',

            'backdrop_zindex'=> 10000,
            'backdrop_background_color'=> '#000000',
            'backdrop_opacity'=> 0.5,

            'close_btn_color'=> '#ffffff',

        ];
        private $res_data;


        /**
         * @return none
         * 
         * @param array  $res_data    response data of requesting payment gateway redirect url
         * @param array $options  options for iframe modal (UI options, theme)
        */
        public function __construct($res_data, $options) {
            $this->res_data = $res_data;

            // configure options array
            if($options) {
                foreach($this->options as $t => $t_val) {
                    if(key_exists($t, $options)) {
                        $this->options[$t] = $options[$t];
                    }
                }
            }
            // var_dump($this->options);
        }


        /**
         * generate html, css and javascript functions for modal
         * 
         * @return none
        */
        function generate() {
            $script = "
            <script>
                setTimeout(() => {
                    const STYLES_ID = 'paymentGatewayModalStyles';
                    const MODAL_ID = 'paymentgatewayModal';
                    const BACKDROP_ID = 'paymentgatewayModalBackdrop';
                    const CLOSE_BTN_ID = 'paymentGatewayModalCloseBtn';

                    {$this->generateStyles()}
                    {$this->generateModal()}
                    {$this->generateBackdrop()}
                    {$this->generateOpenModal()}
                }, 500);
            </script>
            ";

            $script = str_replace(array("\r", "\n", "\t"), '', $script);
            // $script = str_replace("\t", ' ', $script);
            echo $script;

        }


        /**
         * generate modal styles
         * 
         * @return string
        */
        private function generateStyles() {
            $styles = "
            const styleEls = Array.from(document.querySelectorAll('style'));
                if(styleEls.filter(el => el.id === STYLES_ID).length === 0) {
                    let styleEl = document.createElement('style');
                    styleEl.setAttribute('id', STYLES_ID);
                    styleEl.innerHTML = `
                    .payment-gateway-modal{width:{$this->options['modal_width']};height:{$this->options['modal_height']};z-index:{$this->options['modal_zindex']};position:fixed;top:{$this->options['modal_position_top']};left:{$this->options['modal_position_left']};transform:translate(-50%,-50%) scale(.75);background-color:transparent;display:none;opacity:0}.payment-gateway-modal .content{width:100%;height:100%;background-color:transparent;display:flex;flex-direction:column;justify-content:flex-start;align-items:stretch}.payment-gateway-modal .content .close-btn{position:relative;height:40px;display:flex;justify-content:flex-end}.payment-gateway-modal .content .close-btn button,.payment-gateway-modal .content .close-btn button:hover,.payment-gateway-modal .content .close-btn button:focus{border:none;outline:none;text-decoration:none}.payment-gateway-modal .content .close-btn button{position:absolute;top:50%;right:0;transform:translate(0,-50%);background-color:transparent;opacity:.5;cursor:pointer;padding:0}.payment-gateway-modal .content .close-btn button svg{fill:{$this->options['close_btn_color']};transform:scale(1.25)}.payment-gateway-modal .content .close-btn button:hover{opacity:.75}.payment-gateway-modal .content .body{width:100%;flex:1;background-color:{$this->options['modal_background_color']}}.payment-gateway-modal .content .body iframe{width:100%;height:100%;border:none}.payment-gateway-modal-show{display:block;animation-name:payment-gateway-modal-fade-in;animation-duration:0.2s;animation-iteration-count:1;animation-timing-function:ease-in-out}.payment-gateway-modal-show-permenent{transform:translate(-50%,-50%) scale(1);opacity:1}.payment-gateway-modal-remove{animation-name:payment-gateway-modal-fade-out;animation-duration:0.2s;animation-iteration-count:1;animation-timing-function:ease-in-out}.payment-gateway-modal-backdrop{z-index:{$this->options['backdrop_zindex']};background-color:{$this->options['backdrop_background_color']};position:fixed;top:0;left:0;width:100vw;height:100vh;display:none;opacity:0}.payment-gateway-modal-show-backdrop{display:block;animation-name:payment-gateway-modal-backdrop-fade-in;animation-duration:0.2s;animation-iteration-count:1;animation-timing-function:ease-in-out}.payment-gateway-modal-show-backdrop-permenent{opacity:{$this->options['backdrop_opacity']}}.payment-gateway-modal-remove-backdrop{animation-name:payment-gateway-modal-backdrop-fade-out;animation-duration:0.2s;animation-iteration-count:1;animation-timing-function:ease-in-out}@keyframes payment-gateway-modal-fade-in{from{transform:translate(-50%,-50%) scale(.75);opacity:0}to{transform:translate(-50%,-50%) scale(1);opacity:1}}@keyframes payment-gateway-modal-fade-out{from{opacity:1}to{opacity:0}}@keyframes payment-gateway-modal-backdrop-fade-in{from{opacity:0}to{opacity:{$this->options['backdrop_opacity']}}}@keyframes payment-gateway-modal-backdrop-fade-out{from{opacity:{$this->options['backdrop_opacity']}}to{opacity:0}}
                    `;        
                    document.head.appendChild(styleEl);
                }
            ";

            return $styles;
        }


        /**
         * generate modal
         * 
         * @return string
        */
        private function generateModal() {
            return "
                const modalEls = Array.from(document.querySelectorAll(MODAL_ID));
                if(modalEls.length === 0) {
                    let modalEl = document.createElement('div');
                    modalEl.setAttribute('id', MODAL_ID);
                    modalEl.setAttribute('class', 'payment-gateway-modal');
                    modalEl.innerHTML = `
                        <div class='content'>
                            <div class='close-btn'>
                                <button id='paymentGatewayModalCloseBtn'>
                                <svg version='1.1' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'>
                                <path d='M18.984 6.422l-5.578 5.578 5.578 5.578-1.406 1.406-5.578-5.578-5.578 5.578-1.406-1.406 5.578-5.578-5.578-5.578 1.406-1.406 5.578 5.578 5.578-5.578z'></path>
                                </svg>
                                </button>
                            </div>
            
                            <div class='body'>
                                    <iframe 
                                        src={$this->res_data->gateway->redirect_url}
                                        title='Onepay Payment Gateway'
                                    ></iframe>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modalEl);
                }
            ";
        }


        /**
         * generate modal backdrop
         * 
         * @return string
        */
        private function generateBackdrop() {
            $backdrop = '';
            
            if($this->options['backdrop']) {
                $backdrop = "
                    const backdropEls = Array.from(document.querySelectorAll(BACKDROP_ID));
                    if(backdropEls.length === 0) {
                        let backdropEl = document.createElement('div');
                        backdropEl.setAttribute('id', BACKDROP_ID);
                        backdropEl.setAttribute('class', 'payment-gateway-modal-backdrop');
                        document.body.appendChild(backdropEl);
                    }
                ";
            }

            return $backdrop;
        }


        /**
         * generate javascript to trigger modal
         * 
         * @return string
        */
        private function generateOpenModal() {
            $openModal = '
                const modal = document.getElementById(MODAL_ID);
                const backdrop = document.getElementById(BACKDROP_ID);
                const closeBtn = document.getElementById(CLOSE_BTN_ID);

                closeBtn.addEventListener("click", closeModal);
            ';

            if($this->options['on_backdrop_click']) {
                $openModal .= 'backdrop?.addEventListener("click", closeModal);';
            }

            $openModal .= '
                backdrop?.classList.add("payment-gateway-modal-show-backdrop");
                modal.classList.add("payment-gateway-modal-show");

                setTimeout(() => {
                    backdrop?.classList.add("payment-gateway-modal-show-backdrop-permenent");
                    modal.classList.add("payment-gateway-modal-show-permenent");
                }, 150);

                function closeModal() {
                    backdrop?.classList.add("payment-gateway-modal-remove-backdrop");
                    modal.classList.add("payment-gateway-modal-remove");
                
                    setTimeout(() => {
                        backdrop?.classList.remove("payment-gateway-modal-show-backdrop");
                        backdrop?.classList.remove("payment-gateway-modal-show-backdrop-permenent");
                        backdrop?.classList.remove("payment-gateway-modal-remove-backdrop");
                        modal.classList.remove("payment-gateway-modal-show");
                        modal.classList.remove("payment-gateway-modal-show-permenent");
                        modal.classList.remove("payment-gateway-modal-remove");
                    }, 150);
            
                    closeBtn.removeEventListener("click", closeModal);
                    backdrop?.removeEventListener("click", closeModal);
                }
            ';

            return $openModal;

        }
    }