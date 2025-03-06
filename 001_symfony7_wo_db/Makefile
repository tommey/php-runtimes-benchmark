.DEFAULT_GOAL := help
SHELL=bash

cpu ?= 1
memory ?= 1
tool ?= ab
vus ?= 100
duration ?= 30
bench_url ?= http://symfony7site/?firstName=Randomlfirstname&lastName=Randomlastname

dirs := $(sort $(wildcard runtimes/*))
runtimes := $(notdir $(dirs))

MAKE += cpu=$(cpu) memory=$(memory) tool=$(tool) vus=$(vus) duration=$(duration) bench_url="$(bench_url)"

define getLogfile
$(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))/$(1)_$(cpu)vcpu_$(memory)gb_$(tool)_$(vus)_$(duration)_$(shell date +'%Y%m%d%H%M%S').log
endef

ifndef runtime
ifneq ($(filter start/% stop/% bench/%,$(MAKECMDGOALS)),)
runtime := $(word 2,$(subst /, ,$(filter start/% stop/% bench/%,$(MAKECMDGOALS))))
endif
endif

ifdef runtime
ifndef logfile
logfile := $(call getLogfile,$(runtime))
MAKE += runtime=$(runtime) logfile="$(logfile)"
endif
endif

TEE := tee -a $(logfile)

RUN_ENV := VUS=$(vus) DURATION=$(duration) URL="$(bench_url)"
RUN := cd testing-tools && $(RUN_ENV) docker compose --profile extra up -d && $(RUN_ENV) docker compose --profile tool run --rm

help : Makefile
	@echo "Target      Description"
	@echo "--------------------------------------------------------------------------------"
	@sed -n 's/^##//p' $<
	@echo
	@echo "Available tools:"
	@echo "  ab (default)"
	@echo "  k6"
	@echo "  bb - bombardier"
	@echo "  wrk"
	@echo
	@echo "Available runtimes:"
	@for name in $(runtimes); do \
		echo "  "$${name}; \
	done
	@echo
	@echo "Default values:"
	@echo "  cpu       = $(cpu)"
	@echo "  memory    = $(memory) (GB)"
	@echo "  tool      = $(tool)"
	@echo "  vus       = $(vus) (concurrent connections)"
	@echo "  duration  = $(duration) (seconds)"
	@echo "  bench_url = $(bench_url)"

## start/% : Start runtime
start/%:
	cd runtimes/$* && CPU=$(cpu) MEM=$(memory) docker compose up -d --force-recreate --build 2>&1 | $(TEE)

## stop/%  : Stop runtime
stop/%:
	cd runtimes/$* && docker compose down 2>&1 | $(TEE)

## shell/% : Start shell in runtime container
shell/%:
	@docker container exec -it $* bash

## ab      : Run Apache Benchmark
ab:
	$(RUN) ab 2>&1 | $(TEE)

## k6      : Run k6
k6:
	$(RUN) k6 2>&1 | $(TEE)

## bb      : Run bombardier
bb:
	$(RUN) bb 2>&1 | $(TEE)

## wrk     : Run wrk
wrk:
	$(RUN) wrk 2>&1 | $(TEE)

curl:
	$(RUN) curl 2>&1 | $(TEE)

## bench/% : Run benchmark for runtime
bench/%:
	@echo ---- $* ----
	$(MAKE) start/$* runtime=$*
	@sleep 3
	$(MAKE) curl runtime=$*
	@sleep 1
	$(MAKE) $(tool) runtime=$*
	sleep 1
	$(MAKE) stop/$* runtime=$*

## all     : Run benchmark for all runtimes
all:
	@for name in $(runtimes); do \
		make -j1 bench/$${name} runtime=$${name}; \
	done
